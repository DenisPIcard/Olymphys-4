<?php

namespace App\Repository;

use App\Entity\Centrescia;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * RepartprixRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CentresciaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Centrescia::class);
    }


    public function getCentres(CentresciaRepository $cr): QueryBuilder
    {

        return $cr->createQueryBuilder('c')->select('c');
        //->where('e.lettre = :lettre')
        //->setParameter('lettre',$lettre);
    }


}