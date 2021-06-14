<?php

namespace App\Repository;

use App\Entity\Estudiantes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Estudiantes|null find($id, $lockMode = null, $lockVersion = null)
 * @method Estudiantes|null findOneBy(array $criteria, array $orderBy = null)
 * @method Estudiantes[]    findAll()
 * @method Estudiantes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EstudiantesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Estudiantes::class);
    }

    // /**
    //  * @return Estudiantes[] Returns an array of Estudiantes objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Estudiantes
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * @param $id
     * @return Estudiantes|null
     * @throws NonUniqueResultException
     */

    public function findOneByUserId($id): ?Estudiantes
    {
        return $this->createQueryBuilder('e')
            ->innerJoin('e.user','u')
            ->andWhere('u.id = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

}
