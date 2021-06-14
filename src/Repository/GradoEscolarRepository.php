<?php

namespace App\Repository;

use App\Entity\GradoEscolar;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GradoEscolar|null find($id, $lockMode = null, $lockVersion = null)
 * @method GradoEscolar|null findOneBy(array $criteria, array $orderBy = null)
 * @method GradoEscolar[]    findAll()
 * @method GradoEscolar[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GradoEscolarRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GradoEscolar::class);
    }

    // /**
    //  * @return GradoEscolar[] Returns an array of GradoEscolar objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?GradoEscolar
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
