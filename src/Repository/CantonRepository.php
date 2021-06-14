<?php

namespace App\Repository;

use App\Entity\Canton;
use App\Entity\Provincia;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Canton|null find($id, $lockMode = null, $lockVersion = null)
 * @method Canton|null findOneBy(array $criteria, array $orderBy = null)
 * @method Canton[]    findAll()
 * @method Canton[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CantonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Canton::class);
    }

    // /**
    //  * @return Canton[] Returns an array of Canton objects
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

    public function findWithProvincia(){
        return $this->createQueryBuilder('c')
            ->innerJoin(Provincia::class, 'p', Join::WITH, 'c.provincia = p.id')
            ->select('c.id, c.nombre, p.nombre as provincia_nombre')
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findByProvincia($id)
    {
        return $this->createQueryBuilder('canton')
            ->andWhere('canton.provincia = :categoria')
            ->setParameter('categoria', $id)
            ->orderBy('canton.nombre', 'ASC')
            ->getQuery()
            ->getArrayResult()
            ;
    }

    /*
    public function findOneBySomeField($value): ?Canton
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
