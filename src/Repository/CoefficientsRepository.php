<?php

namespace App\Repository;

use App\Entity\Coefficients;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Coefficients|null find($id, $lockMode = null, $lockVersion = null)
 * @method Coefficients|null findOneBy(array $criteria, array $orderBy = null)
 * @method Coefficients[]    findAll()
 * @method Coefficients[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CoefficientsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Coefficients::class);
    }

    // /**
    //  * @return Coefficients[] Returns an array of Coefficients objects
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
    public function findOneBySomeField($value): ?Coefficients
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
