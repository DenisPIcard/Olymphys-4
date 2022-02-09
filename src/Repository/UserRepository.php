<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    private RequestStack $requestStack;

    public function __construct(ManagerRegistry $registry, RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;

        parent::__construct($registry, User::class);


    }

    public function getProfautorisation(UserRepository $er): QueryBuilder//Liste des prof sans autorisation photos
    {

        $edition = $_SESSION['_sf2_attributes']['edition'];

        $qb = $er->createQueryBuilder('p');
        return $er->createQueryBuilder('u')
            ->leftJoin('u.autorisationphotos', 'aut')
            ->andWhere('aut.edition != :edition')
            ->setParameter('edition', $edition)
            ->andWhere($qb->expr()->like('u.roles', ':roles'))
            ->setParameter('roles', 'a:2:{i:0;s:9:"ROLE_PROF";i:1;s:9:"ROLE_USER";}')
            ->orWhere($qb->expr()->like('u.roles', ':roles'))
            ->setParameter('roles', '%i:0;s:9:"ROLE_PROF";i:2;s:9:"ROLE_USER";%')
            ->addOrderBy('u.nom', 'ASC');
    }

    public function getProfesseur(UserRepository $er): QueryBuilder//Liste des profs
    {

        $qb = $er->createQueryBuilder('p');
        return $er->createQueryBuilder('u')
            ->andWhere($qb->expr()->like('u.roles', ':roles'))
            ->setParameter('roles', '%i:0;s:9:"ROLE_PROF";i:2;s:9:"ROLE_USER";%')
            ->orWhere($qb->expr()->like('u.roles', ':role'))
            ->setParameter('role', '%a:2:{i:0;s:9:"ROLE_PROF";i:1;s:9:"ROLE_USER";}%')
            ->addOrderBy('u.nom', 'ASC');
    }

    public function getHote(UserRepository $er): QueryBuilder
    {//listes de membres du comité (pour la table equipes selectionnes)

        $qb = $er->createQueryBuilder('p');
        return $er->createQueryBuilder('h')
            ->select('h')
            ->andWhere($qb->expr()->like('h.roles', ':roles'))
            ->setParameter('roles', 'a:2:{i:0;s:11:"ROLE_COMITE";i:1;s:9:"ROLE_USER";}')
            ->addOrderBy('h.nom', 'ASC');
    }

    public function getInterlocuteur(UserRepository $er): QueryBuilder
    {//listes de membres du comité (pour la table equipes selectionnes)

        $qb = $er->createQueryBuilder('p');
        return $er->createQueryBuilder('h')
            ->select('h')
            ->andWhere($qb->expr()->like('h.roles', ':roles'))
            ->setParameter('roles', 'a:2:{i:0;s:9:"ROLE_JURY";i:1;s:9:"ROLE_USER";}')
            ->addOrderBy('h.nom', 'ASC');

    }

    public function getEquipes(UserRepository $er): QueryBuilder
    {


    }
}
