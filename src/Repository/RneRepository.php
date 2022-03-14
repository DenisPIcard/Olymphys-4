<?php

namespace App\Repository;

use App\Entity\Rne;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Rne|null find($id, $lockMode = null, $lockVersion = null)
 * @method Rne|null findOneBy(array $criteria, array $orderBy = null)
 * @method Rne[]    findAll()
 * @method Rne[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rne::class);
    }

}