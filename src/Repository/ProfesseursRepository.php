<?php

namespace App\Repository;

use App\Entity\Professeurs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Professeurs|null find($id, $lockMode = null, $lockVersion = null)
 * @method Professeurs|null findOneBy(array $criteria, array $orderBy = null)
 * @method Professeurs[]    findAll()
 * @method Professeurs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProfesseursRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Professeurs::class);
    }
}

