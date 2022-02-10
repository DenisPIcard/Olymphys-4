<?php


namespace App\Repository;

use App\Entity\Equipes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * EquipesRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class EquipesRepository extends ServiceEntityRepository
{
    private RequestStack $requestStack;

    public function __construct(ManagerRegistry $registry, RequestStack $requestStack)
    {
        parent::__construct($registry, Equipes::class);
        $this->requestStack = $requestStack;
    }

    public function getEquipe(EquipesRepository $er): QueryBuilder
    {

        return $er->createQueryBuilder('e')->select('e');

    }

    public function getEquipes(EquipesRepository $er): QueryBuilder
    {
        return $er->createQueryBuilder('e')
            ->where('e.visite IS  NULL')
            ->orderBy('e.lettre', 'ASC');
    }


    public function getEquipesVisites()
    {
        $query = $this->createQueryBuilder('e')
            ->leftJoin('e.visite', 'v')
            ->addSelect('v')
            ->leftJoin('e.equipeinter', 'eq')
            ->orderBy('eq.lettre')
            ->getQuery();

        return $query->getResult();
    }

    public function getEquipesPrix()
    {
        $query = $this->createQueryBuilder('e')
            ->leftJoin('e.prix', 'p')
            ->addSelect('p')
            ->orderBy('e.classement')
            ->getQuery();

        return $query->getResult();
    }

    public function getEquipesPhrases()
    {
        $query = $this->createQueryBuilder('e')
            ->leftJoin('e.phrases', 'p')
            ->leftJoin('e.liaison', 'l')
            ->addSelect('p')
            ->addSelect('l')
            ->orderBy('e.classement', 'ASC', 'e.lettre', 'ASC')
            ->getQuery();

        return $query->getResult();
    }


    public function getEquipesCadeaux()
    {
        $query = $this->createQueryBuilder('e')
            ->leftJoin('e.cadeau', 'c')
            ->addSelect('c')
            ->orderBy('e.rang')
            ->getQuery();

        return $query->getResult();
    }

    public function getEquipesPalmares()
    {
        $query = $this->createQueryBuilder('e')
            ->leftJoin('e.cadeau', 'c')
            ->addSelect('c')
            ->leftJoin('e.phrases', 'f')
            ->leftJoin('e.liaison', 'l')
            ->addSelect('f')
            ->addSelect('l')
            ->leftJoin('e.prix', 'p')
            ->addSelect('p')
            ->leftJoin('e.visite', 'v')
            ->addSelect('v')
            ->leftJoin('e.equipeinter', 'i')
            ->addSelect('i')
            ->orderBy('e.classement', 'ASC', 'e.lettre', 'ASC')
            ->getQuery();

        return $query->getResult();
    }

    public function getEquipesPalmaresJury() //identique à getEquipesPalmares() ?
    {
        $query = $this->createQueryBuilder('e')
            ->leftJoin('e.cadeau', 'c')
            ->addSelect('c')
            ->leftJoin('e.phrases', 'f')
            ->leftJoin('e.liaison', 'l')
            ->addSelect('f')
            ->addSelect('l')
            ->leftJoin('e.prix', 'p')
            ->addSelect('p')
            ->leftJoin('e.visite', 'v')
            ->addSelect('v')
            ->leftJoin('e.equipeinter', 'i')
            ->addSelect('i')
            ->orderBy('e.classement', 'DESC', 'e.lettre', 'ASC')
            ->getQuery();

        return $query->getResult();
    }

    public function getEquipesAccueil()
    {
        $query = $this->createQueryBuilder('e')
            ->Join('e.equipeinter', 'i')
            ->addSelect('i')
            ->orderBy('e.lettre')
            ->getQuery();

        return $query->getResult();
    }

    public function miseEnOrdre()
    {
        $query = $this->createQueryBuilder('e')
            ->orderBy('e.ordre', 'ASC')
            ->getQuery();

        return $query->getResult();
    }

    public function classement($niveau, $offset, $nbreprix)
    {

        $queryBuilder = $this->createQueryBuilder('e');

        if ($niveau == 0) {
            $queryBuilder
                ->orderBy('e.total', 'DESC');

        } else {
            $limit = $nbreprix;
            $queryBuilder
                ->select('e')
                ->orderBy('e.rang', 'ASC')
                ->setFirstResult($offset)
                ->setMaxResults($limit);
        }


        $query = $queryBuilder->getQuery();

        return $query->getResult();
    }

    public function palmares($niveau, $offset, $nbreprix)
    {

        $queryBuilder = $this->createQueryBuilder('e');  // identique à classement

        if ($niveau == 0) {
            $queryBuilder
                ->orderBy('e.rang', 'ASC');
        } else {
            $limit = $nbreprix;
            $queryBuilder
                ->select('e')
                ->orderBy('e.rang', 'ASC')
                ->setFirstResult($offset)
                ->setMaxResults($limit);
        }

        $query = $queryBuilder->getQuery();

        return $query->getResult();
    }


}