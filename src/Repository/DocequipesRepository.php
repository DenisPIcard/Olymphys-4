<?php

namespace App\Repository;

use App\Entity\Docequipes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Docequipes|null find($id, $lockMode = null, $lockVersion = null)
 * @method Docequipes|null findOneBy(array $criteria, array $orderBy = null)
 * @method Docequipes[]    findAll()
 * @method Docequipes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocequipesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Docequipes::class);
    }

    // /**
    //  * @return Docequipes[] Returns an array of Docequipes objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Docequipes
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
