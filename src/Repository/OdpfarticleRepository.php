<?php

namespace App\Repository;

use App\Entity\Odpf\Odpfarticle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Odpfarticle|null find($id, $lockMode = null, $lockVersion = null)
 * @method Odpfarticle|null findOneBy(array $criteria, array $orderBy = null)
 * @method Odpfarticle[]    findAll()
 * @method Odpfarticle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OdpfarticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Odpfarticle::class);
    }

    // /**
    //  * @return Odpfarticles[] Returns an array of Odpfarticles objects
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
    public function findOneBySomeField($value): ?Odpfarticles
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
