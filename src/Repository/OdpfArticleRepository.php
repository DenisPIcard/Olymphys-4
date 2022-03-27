<?php

namespace App\Repository;

use App\Entity\OdpfArticle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @method OdpfArticle|null find($id, $lockMode = null, $lockVersion = null)
 * @method OdpfArticle|null findOneBy(array $criteria, array $orderBy = null)
 * @method OdpfArticle[]    findAll()
 * @method OdpfArticle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OdpfArticleRepository extends ServiceEntityRepository
{
    private RequestStack $requestStack;

    public function __construct(ManagerRegistry $registry, RequestStack $requestStack)
    {
        parent::__construct($registry, OdpfArticle::class);
        $this->requestStack = $requestStack;
    }

    public function actuspaginees($choix, $pageCourante = 1): array
    {
        $categorie = 'Actus';
        $titre='Actus';
        $edition = $this->requestStack->getSession()->get('edition');
        $listActus = $this->createQueryBuilder('e')
            ->select('e')
            ->andWhere('e.choix =:choix')
            ->setParameter('choix', $choix)
            ->orderBy('e.id', 'DESC')
            ->getQuery()
            ->getResult()
           ;
        $limit = 5;
        $totactus=count($listActus);
        $nbpages=intval(ceil($totactus/$limit));
        $affActus=array_chunk($listActus,$limit);

        //dd($affActus);

        return $tab=['categorie' =>$categorie,
            'choix' =>$choix,
            'titre' =>$titre,
            'edition' =>$edition,
            'nbpages' =>$nbpages,
            'pageCourante'=>$pageCourante,
            'affActus' => $affActus ];
    }

    /**
     * Paginator Helper
     *
     * Pass through a query object, current page & limit
     * the offset is calculated from the page and limit
     * returns an `Paginator` instance, which you can call the following on:
     *
     *     $paginator->getIterator()->count() # Total fetched (ie: `5` posts)
     *     $paginator->count() # Count of ALL posts (ie: `20` posts)
     *     $paginator->getIterator() # ArrayIterator
     *
     * @param $query
     * @param integer $page Current page (defaults to 1)
     * @param integer $limit The total number per page (defaults to 5)
     *
     * @return Paginator
     */
    public function paginate($query, int $page = 1, int $limit = 5): Paginator
    {
        $paginator = new Paginator($query);

        $paginator->getQuery()
            ->setFirstResult($limit * ($page - 1)) // Offset
            ->setMaxResults($limit); // Limit

        return $paginator;
    }
    // /**
    //  * @return OdpfArticle[] Returns an array of OdpfArticle objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?OdpfArticle
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

}
