<?php

namespace App\Repository;

use App\Entity\LibroActivado;
use App\Entity\Recurso;
use App\Entity\Role;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Recurso|null find($id, $lockMode = null, $lockVersion = null)
 * @method Recurso|null findOneBy(array $criteria, array $orderBy = null)
 * @method Recurso[]    findAll()
 * @method Recurso[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecursoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recurso::class);
    }

    // /**
    //  * @return Recurso[] Returns an array of Recurso objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Recurso
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * @param int $id
     * @param string $role
     * @param null $book
     * @return array|null
     */
    public function findRecursosById(int $id, string $role, $book = null)
    {
        $resultArray = null;

        $qb = $this->createQueryBuilder('recurso');

        if ($role === Role::ROLE_PROFESOR) {
            $qb
                ->leftJoin('recurso.libro', 'libro')
                ->leftJoin('libro.libroActivados', 'libroActivados')
                ->leftJoin('libroActivados.profesor', 'profesor')
                ->where('recurso.paraDocente = true')
                ->andWhere('profesor.id=:id')
                ->orderBy('libro.nombre');
        } elseif ($role === Role::ROLE_ESTUDIANTE) {
            $qb
                ->leftJoin('recurso.libro', 'libro')
                ->leftJoin('libro.libroActivados', 'libroActivados')
                ->leftJoin('libroActivados.estudiante', 'estudiante')
                ->where('recurso.paraPlataforma = true')
                ->andWhere('estudiante.id=:id')
                ->orderBy('libro.nombre');
        }
        if ($book) {
            $qb
                ->andWhere('libro.id=:book')
                ->setParameter('book', $book);
        }
        $qb->setParameter('id', $id);
        $result = $qb->getQuery()->getResult();

        if (array_key_exists(0, $result)) {
            $hasted = $result[0]->getLibro()->getNombre();
            $resultArray = $this->normalizeArray($result, $hasted);
        }

        return $resultArray;
    }

    public function findRecursos($book = null)
    {
        $resultArray = null;

        $qb = $this->createQueryBuilder('recurso');


        $qb
            ->leftJoin('recurso.libro', 'libro')
            // ->leftJoin('libro.libroActivados', 'libroActivados')
            ->orderBy('libro.nombre');

        if ($book) {
            $qb
                ->andWhere('libro.id=:book')
                ->setParameter('book', $book);
        }
        $result = $qb->getQuery()->getResult();

        if (array_key_exists(0, $result)) {
            $hasted = $result[0]->getLibro()->getNombre();
            $resultArray = $this->normalizeArray($result, $hasted);
        }

        return $resultArray;
    }

    private function normalizeArray(array $array, string $hasted): ?array
    {
        $row = 0;
        $resultArray = null;

        foreach ($array as $key => $value) {
            if ($hasted === $value->getLibro()->getNombre() && $resultArray != null) {
                array_push($resultArray[$row], $value);
            } else if ($resultArray === null) {
                $resultArray = [$row => array()];
                array_push($resultArray[$row], $value);
            } else if ($hasted !== $value->getLibro()->getNombre()) {
                $resultArray[] = array();
                $hasted = $value->getLibro()->getNombre();
                array_push($resultArray[++$row], $value);
            }
        }
        return $resultArray;
    }
}
