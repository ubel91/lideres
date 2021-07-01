<?php

namespace App\Repository;

use App\Entity\Libro;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Libro|null find($id, $lockMode = null, $lockVersion = null)
 * @method Libro|null findOneBy(array $criteria, array $orderBy = null)
 * @method Libro[]    findAll()
 * @method Libro[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LibroRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Libro::class);
    }

    // /**
    //  * @return Libro[] Returns an array of Libro objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Libro
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findByUserEstudiante($id)
    {
        return $this->createQueryBuilder('l')
//            ->select('l.id', 'l.nombre', 'l.portada')
            ->innerJoin('l.libroActivados','la')
            ->innerJoin('la.estudiante', 'e')
            ->innerJoin('e.user','u')
            ->andWhere('u.id = :val')
            ->setParameter('val', $id)
            ->orderBy('l.nombre', 'ASC')
            ->getQuery()
            ->getResult(Query::HYDRATE_OBJECT)
            ;
    }

    public function findByUserDocente($id)
    {
        return $this->createQueryBuilder('l')
            ->select('l.id', 'l.nombre', 'l.portada')
            ->innerJoin('l.libroActivados','la')
            ->innerJoin('la.profesor', 'p')
            ->innerJoin('p.user','u')
            ->andWhere('u.id = :val')
            ->setParameter('val', $id)
            ->orderBy('l.nombre', 'ASC')
            ->getQuery()
            ->getResult(Query::HYDRATE_OBJECT)
            ;
    }

    public function findByRoleEst()
    {
        return $this->createQueryBuilder('l')
//            ->select('l.id', 'l.nombre', 'l.portada', 'l.para_estudiante')
            ->andWhere('l.para_estudiante = :val')
            ->setParameter('val', 1)
            ->orderBy('l.nombre', 'ASC')
            ->getQuery()
            ->getResult(Query::HYDRATE_OBJECT)
            ;
    }

    public function findByRoleDoc()
    {
        return $this->createQueryBuilder('l')
//            ->select('l.id', 'l.nombre', 'l.portada', 'l.para_docente')
            ->andWhere('l.para_docente = :val')
            ->setParameter('val', 1)
            ->orderBy('l.nombre', 'ASC')
            ->getQuery()
            ->getResult(Query::HYDRATE_OBJECT)
            ;
    }

    public function findByRoleEstAndNotActivated()
    {
        return $this->createQueryBuilder('l')
//            ->select('l.id', 'l.nombre', 'l.portada', 'l.para_estudiante')
            // ->leftJoin('l.libroActivados', 'la', 'WITH')
            // ->leftJoin('la.estudiante', 'e', 'WITH', 'e.id = la.estudiante')
            ->andWhere('l.para_estudiante = :val')
            ->setParameter('val', 1)
            ->orderBy('l.nombre', 'ASC')
            ->getQuery()
            ->getResult(Query::HYDRATE_OBJECT)
            ;
    }

    public function findByRoleDocAndNotActivated()
    {
        return $this->createQueryBuilder('l')
//            ->select('l.id', 'l.nombre', 'l.portada', 'l.para_estudiante')
            // ->leftJoin('l.libroActivados', 'la', 'WITH')
            // ->leftJoin('la.profesor', 'p', 'WITH', 'p.id = la.profesor')
            ->andWhere('l.para_docente = :val')
            ->setParameter('val', 1)
            ->orderBy('l.nombre', 'ASC')
            ->getQuery()
            ->getResult(Query::HYDRATE_OBJECT)
            ;
    }

//    public function findByRoleEstAndNotActivated($id)
//    {
//        return $this->createQueryBuilder('l')
////            ->select('l.id', 'l.nombre', 'l.portada', 'l.para_estudiante')
//            ->leftJoin('l.libroActivados', 'la', 'WITH')
//            ->leftJoin('la.estudiante', 'e', 'WITH', 'e.id = la.estudiante')
//            ->andWhere('l.para_estudiante = :val')
//            ->andWhere('e.id = :id')
//            ->setParameter('val', 1)
//            ->setParameter('id', $id)
//            ->orderBy('l.nombre', 'ASC')
//            ->getQuery()
//            ->getResult(Query::HYDRATE_OBJECT)
//            ;
//    }

}
