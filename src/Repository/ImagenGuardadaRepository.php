<?php

namespace App\Repository;

use App\Entity\ImagenGuardada;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ImagenGuardada|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImagenGuardada|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImagenGuardada[]    findAll()
 * @method ImagenGuardada[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImagenGuardadaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImagenGuardada::class);
    }

    // /**
    //  * @return ImagenGuardada[] Returns an array of ImagenGuardada objects
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
    public function findOneBySomeField($value): ?ImagenGuardada
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * @return ImagenGuardada[] Returns an array of Notas objects
     */

    public function findImagenesByUser($id, $unidad_id)
    {
        return $this->createQueryBuilder('i')
            ->where('i.user = :val AND i.unidad = :val2')
            ->setParameter('val', $id)
            ->setParameter('val2', $unidad_id)
            ->orderBy('i.id', 'ASC')
            ->getQuery()
            ->getResult(AbstractQuery::HYDRATE_ARRAY)
            ;
    }
}
