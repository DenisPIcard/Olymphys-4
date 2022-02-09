<?php

namespace App\Repository;

use App\Entity\Jury;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Jury|null find($id, $lockMode = null, $lockVersion = null)
 * @method Jury|null findOneBy(array $criteria, array $orderBy = null)
 * @method Jury[]    findAll()
 * @method Jury[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JuryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Jury::class);
    }

}
