<?php

namespace App\Repository;

use App\Entity\SubCategoria;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SubCategoria|null find($id, $lockMode = null, $lockVersion = null)
 * @method SubCategoria|null findOneBy(array $criteria, array $orderBy = null)
 * @method SubCategoria[]    findAll()
 * @method SubCategoria[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubCategoriaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SubCategoria::class);
    }

    // /**
    //  * @return SubCategoria[] Returns an array of SubCategoria objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SubCategoria
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findByCategoria($id)
    {
        return $this->createQueryBuilder('sub_categoria')
            ->andWhere('sub_categoria.categoria = :categoria')
            ->setParameter('categoria', $id)
            ->orderBy('sub_categoria.nombre', 'ASC')
            ->getQuery()
            ->getArrayResult()
            ;
    }

}
