<?php

namespace App\Repository;

use App\Entity\Visites;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class VisitesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, SessionInterface $session)
    {
        parent::__construct($registry, Visites::class);
        $this->idvisite = $session->get('idvisite');
    }

    public static function getVisites(VisitesRepository $vr): QueryBuilder
    {

        $b = $vr->createQueryBuilder('v')->select('v')
            ->where('v.attribue =:value')
            ->setParameter('value', 'FALSE')
            ->orWhere('v.attribue IS null');

        if ($vr->idvisite != null) {
            $b->orWhere('v.id =:id')
                ->setParameter('id', $vr->idvisite->getId());
        }

        return $b;

    }


}


