<?php

namespace App\Repository;

use App\Entity\TipoRecurso;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TipoRecurso|null find($id, $lockMode = null, $lockVersion = null)
 * @method TipoRecurso|null findOneBy(array $criteria, array $orderBy = null)
 * @method TipoRecurso[]    findAll()
 * @method TipoRecurso[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TipoRecursoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TipoRecurso::class);
    }

    // /**
    //  * @return TipoRecurso[] Returns an array of TipoRecurso objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TipoRecurso
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
