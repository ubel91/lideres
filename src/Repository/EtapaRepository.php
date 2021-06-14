<?php

namespace App\Repository;

use App\Entity\Etapa;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Etapa|null find($id, $lockMode = null, $lockVersion = null)
 * @method Etapa|null findOneBy(array $criteria, array $orderBy = null)
 * @method Etapa[]    findAll()
 * @method Etapa[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EtapaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Etapa::class);
    }

    // /**
    //  * @return Etapa[] Returns an array of Etapa objects
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
    public function findOneBySomeField($value): ?Etapa
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
