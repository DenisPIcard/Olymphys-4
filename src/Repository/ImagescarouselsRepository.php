<?php

namespace App\Repository;

use App\Entity\Imagescarousels;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Imagescarousels|null find($id, $lockMode = null, $lockVersion = null)
 * @method Imagescarousels|null findOneBy(array $criteria, array $orderBy = null)
 * @method Imagescarousels[]    findAll()
 * @method Imagescarousels[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImagescarouselsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Imagescarousels::class);
    }

    // /**
    //  * @return Imagescarousels[] Returns an array of Imagescarousels objects
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
    public function findOneBySomeField($value): ?Imagescarousels
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
