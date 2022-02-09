<?php

namespace App\Repository;

use App\Entity\Elevesinter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * ElevesinterRepository
 */
class ElevesinterRepository extends ServiceEntityRepository
{
    private RequestStack $requestStack;


    public function __construct(ManagerRegistry $registry, RequestStack $requestStack)
    {
        parent::__construct($registry, Elevesinter::class);
        $this->requestStack = $requestStack;
    }

    public function getEleve(ElevesinterRepository $er): QueryBuilder
    {
        $session = $this->requestStack->getSession();
        $edition = $session->get('edition');

        return $er->createQueryBuilder('e')
            ->where('e.autorisationphotos is null')
            ->LeftJoin('e.equipe', 'eq')
            ->andWhere('eq.edition = :edition')
            ->setParameter('edition', $edition)
            ->addOrderBy('eq.numero', 'ASC');
    }

}


