<?php

namespace App\Repository;

use App\Entity\Codigo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Codigo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Codigo|null findOneBy(array $criteria, array $orderBy = null)
 * @method Codigo[]    findAll()
 * @method Codigo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CodigoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Codigo::class);
    }

    // /**
    //  * @return Codigo[] Returns an array of Codigo objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Codigo
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findCodigos()
    {
        return $this->createQueryBuilder('c')
            ->select('c.codebook')
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findCodigosByBook($id)
    {
        return $this->createQueryBuilder('c')
            ->select('c.codebook')
            ->innerJoin('c.libro', 'l')
            ->andWhere('l.id = :val')
            ->setParameter('val', $id)
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }
    public function findCodigosByBookAll($id)
    {
        return $this->createQueryBuilder('c')
            ->select('c.codebook')
            ->addSelect('c.fechaInicio')
            ->addSelect('c.fechaFin')
            ->innerJoin('c.libro', 'l')
            ->andWhere('l.id = :val')
            ->setParameter('val', $id)
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findDatesByCode($code)
    {
        return $this->createQueryBuilder('c')
            ->select('c.fechaInicio', 'c.fechaFin')
            ->andWhere('c.codebook = :val')
            ->setParameter('val', $code)
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }
}
