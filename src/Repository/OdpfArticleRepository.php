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

    public function actuspaginees(): array
    {
        $categorie = 'Actus';
        $titre='Actus';
        $choix='actus';
        $edition = $this->requestStack->getSession()->get('edition');
        $pageCourante=$this->requestStack->getSession()->get('pageCourante');
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

        return [
            'categorie' =>$categorie,
            'choix' =>$choix,
            'titre'=>$titre,
            'edition' =>$edition,
            'nbpages' =>$nbpages,
            'pageCourante'=>$pageCourante,
            'affActus' => $affActus
            ];
    }



}
