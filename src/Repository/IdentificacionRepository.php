<?php

namespace App\Repository;

use App\Entity\Identificacion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Identificacion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Identificacion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Identificacion[]    findAll()
 * @method Identificacion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IdentificacionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Identificacion::class);
    }

    // /**
    //  * @return Identificacion[] Returns an array of Identificacion objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Identificacion
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
