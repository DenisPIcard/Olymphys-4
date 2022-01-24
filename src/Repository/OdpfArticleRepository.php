<?php

namespace App\Repository;

use App\Entity\Odpf\OdpfArticle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OdpfArticle|null find($id, $lockMode = null, $lockVersion = null)
 * @method OdpfArticle|null findOneBy(array $criteria, array $orderBy = null)
 * @method OdpfArticle[]    findAll()
 * @method OdpfArticle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OdpfArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OdpfArticle::class);
    }

    // /**
    //  * @return OdpfArticle[] Returns an array of OdpfArticle objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?OdpfArticle
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
