<?php

namespace App\Repository;

use App\Entity\Cadeaux;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CadeauxRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry, SessionInterface $session)
    {
        parent::__construct($registry, Cadeaux::class);
        $this->idcadeau = $session->get('idcadeau');
    }

    public function getCadeaux(CadeauxRepository $cr): QueryBuilder
    {

        $qb = $cr->createQueryBuilder('c')->select('c')
            ->where('c.attribue =:value')
            ->setParameter('value', 'FALSE')
            ->orWhere('c.attribue IS null');
        if ($cr->idcadeau != null) {
            $qb->orWhere('c.id =:id')
                ->setParameter('id', $cr->idcadeau->getId());
        }
        return $qb;

    }


}