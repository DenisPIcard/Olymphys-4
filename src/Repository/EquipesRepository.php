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
    public function __construct(ManagerRegistry $registry, RequestStack $requestStack)
    {
        parent::__construct($registry, Equipes::class);
        $this->requestStack = $requestStack;
    }

    public function getEquipe(EquipesRepository $er): QueryBuilder
    {

        return $er->createQueryBuilder('e')->select('e');
        //->where('e.lettre = :lettre')
        //->setParameter('lettre',$lettre);
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

    public function getEquipesPalmaresJury()
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
                //->orderBy('e.rang', 'ASC');
        } else {
            $limit = $nbreprix;
            $queryBuilder
                ->select('e')
                //->orderBy('e.total', 'DESC')
                ->orderBy('e.rang', 'ASC')
                ->setFirstResult($offset)
                ->setMaxResults($limit);
        }

        // on r??cup??re la query
        $query = $queryBuilder->getQuery();

        // getResult() ex??cute la requ??te et retourne un tableau contenant les r??sultats sous forme d'objets.
        // Utiliser getArrayResult en cas d'affichage simple : le r??sultat est sous forme de tableau : plus rapide que getResult()
        $results = $query->getResult();

        // on retourne ces r??sultats
        return $results;
    }

    public function palmares($niveau, $offset, $nbreprix)
    {

        $queryBuilder = $this->createQueryBuilder('e');  // e est un alias, un raccourci donn?? ?? l'entit?? du repository. 1??re lettre du nom de l'entit??

        // On ajoute des crit??res de tri, etc.

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

        // on r??cup??re la query
        $query = $queryBuilder->getQuery();

        // getResult() ex??cute la requ??te et retourne un tableau contenant les r??sultats sous forme d'objets.
        // Utiliser getArrayResult en cas d'affichage simple : le r??sultat est sous forme de tableau : plus rapide que getResult()
        $results = $query->getResult();

        // on retourne ces r??sultats
        return $results;
    }

    public function MyFindOne($id)
    {
        $queryBuilder = $this->createQueryBuilder('e');  // e est un alias, un raccourci donn?? ?? l'entit?? du repository. 1??re lettre du nom de l'entit??

        // On ajoute des crit??res de tri, etc.
        $queryBuilder
            ->where('e.id=:id')
            ->setParameter('id', $id);

        // on r??cup??re la query
        $query = $queryBuilder->getQuery();

        // on r??cup??re les r??sultats ?? partir de la Query
        $results = $query->getResult();

        // on retourne ces r??sultats
        return $results;
    }

    public function MyFindIdByLettre($lettre)
    {
        $queryBuilder = $this->createQueryBuilder('e');  // e est un alias, un raccourci donn?? ?? l'entit?? du repository. 1??re lettre du nom de l'entit??

        // On ajoute des crit??res de tri, etc.
        $queryBuilder
            ->select('e.id')
            ->leftJoin('e.equipeinter', 'eq')
            ->where('eq.lettre=:lettre')
            ->setParameter('lettre', $lettre);

        // on r??cup??re la query
        $query = $queryBuilder->getQuery();

        // on r??cup??re les r??sultats ?? partir de la Query
        $value = $query->getSingleScalarResult();

        // on retourne ces r??sultats
        return $value;
    }

}