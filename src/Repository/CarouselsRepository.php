<?php

namespace App\Repository;

use App\Entity\OdpfCarousels;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OdpfCarousels|null find($id, $lockMode = null, $lockVersion = null)
 * @method OdpfCarousels|null findOneBy(array $criteria, array $orderBy = null)
 * @method OdpfCarousels[]    findAll()
 * @method OdpfCarousels[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CarouselsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OdpfCarousels::class);
    }

    // /**
    //  * @return OdpfCarousels[] Returns an array of OdpfCarousels objects
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
    public function findOneBySomeField($value): ?OdpfCarousels
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
