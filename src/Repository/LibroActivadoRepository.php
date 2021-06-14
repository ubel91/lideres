<?php

namespace App\Repository;

use App\Entity\LibroActivado;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LibroActivado|null find($id, $lockMode = null, $lockVersion = null)
 * @method LibroActivado|null findOneBy(array $criteria, array $orderBy = null)
 * @method LibroActivado[]    findAll()
 * @method LibroActivado[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LibroActivadoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LibroActivado::class);
    }

    // /**
    //  * @return LibroActivado[] Returns an array of LibroActivado objects
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
    public function findOneBySomeField($value): ?LibroActivado
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findOneByLibroProfesor($idLibro, $idEstudiante): ?LibroActivado
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.libro = :val')
            ->andWhere('l.estudiante = :val1')
            ->setParameter('val', $idLibro)
            ->setParameter('val1', $idEstudiante)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
    public function findOneByLibroDocente($idLibro, $idProfesor): ?LibroActivado
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.libro = :val')
            ->andWhere('l.profesor = :val1')
            ->setParameter('val', $idLibro)
            ->setParameter('val1', $idProfesor)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function findByUserEstudiante($id)
    {
        return $this->createQueryBuilder('la')
            ->innerJoin('la.estudiante', 'e')
            ->innerJoin('e.user', 'u')
            ->andWhere('u.id = :val')
            ->setParameter('val', $id)
            ->orderBy('la.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }
    public function findByUserProfesor($id)
    {
        return $this->createQueryBuilder('la')
            ->innerJoin('la.profesor', 'p')
            ->innerJoin('p.user', 'u')
            ->andWhere('u.id = :val')
            ->setParameter('val', $id)
            ->orderBy('la.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findLibrosActivadosByEst($id)
    {
        $dql = 'SELECT c FROM App:Codigo c INNER JOIN App:Libro l INNER JOIN App:LibroActivado la WITH la.codigoActivacion = c.codebook WHERE la.estudiante = :val AND :val2 BETWEEN c.fechaInicio AND c.fechaFin';
        $query = $this->getEntityManager()->createQuery($dql)->setParameter('val', $id)->setParameter('val2', new \DateTime());
        return  $query->getResult();
    }

    public function findLibrosActivadosByDoc($id)
    {
        $dql = 'SELECT c FROM App:Codigo c INNER JOIN App:Libro l INNER JOIN App:LibroActivado la WITH la.codigoActivacion = c.codebook WHERE la.profesor = :val AND :val2 BETWEEN c.fechaInicio AND c.fechaFin';
        $query = $this->getEntityManager()->createQuery($dql)->setParameter('val', $id)->setParameter('val2', new \DateTime());
        return  $query->getResult();
    }
}
