<?php

namespace App\Service;

use App\Entity\Odpf\OdpfArticle;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;



class OdpfListeEquipes
{
    private EntityManagerInterface $em;
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $em)
    {
        $this->requestStack = $requestStack;
        $this->em = $em;
    }

    public function getArray($choix): array
    {
        $edition = $this->requestStack->getSession()->get('edition');
        $repo = $this->em->getRepository(OdpfArticle::class);
        $article = $repo->findOneBy(['choix' => $choix]);
        $categorie = $article->getCategorie();
        $titre = $article->getTitre().' '.$edition->getEd().'e édition';
        $repositoryEquipesadmin = $this->em->getRepository('App:Equipesadmin');
        $editionpassee=$this->em->getRepository('App:Odpf\OdpfEditionsPassees')->findOneBy(['edition'=>$edition->getEd()]);
        $photoparrain='odpf-archives/'.$editionpassee->getEdition().'/parrain/'.$editionpassee->getPhotoParrain();
        $parrain=$editionpassee->getNomParrain();
        $titreparrain=$editionpassee->getTitreParrain();
        $affiche='odpf-archives/'.$editionpassee->getEdition().'/affiche/affiche'.$editionpassee->getEdition().'.jpg';
        $repositoryUser = $this->em->getRepository('App:User');
        $repositoryRne = $this->em->getRepository('App:Rne');
        $listEquipes = $repositoryEquipesadmin->createQueryBuilder('e')
            ->select('e')
            ->andWhere('e.edition =:edition')
            ->setParameter('edition', $edition)
            ->orderBy('e.numero', 'ASC')
            ->getQuery()
            ->getResult();
        foreach ($listEquipes as $equipe) {
            $numero = $equipe->getNumero();
            $rne = $equipe->getRne();
            $lycee[$numero] = $repositoryRne->findByRne($rne);
            $idprof1 = $equipe->getIdProf1();
            $prof1[$numero] = $repositoryUser->findById($idprof1);
            $idprof2 = $equipe->getIdProf2();
            $prof2[$numero] = $repositoryUser->findById($idprof2);

        }
        //dd($listEquipes);
        if($listEquipes != []){

            return ['listEquipes' => $listEquipes,
                'prof1' => $prof1,
                'prof2' => $prof2,
                'lycee' => $lycee,
                'choix' => $choix,
                'edition' => $edition,
                'titre' => $titre,
                'categorie' => $categorie,
                'parrain'=>$parrain,
                'photoparrain'=>$photoparrain,
                'titreparrain'=>$titreparrain,
                'affiche'=>$affiche
            ];
        }
        else {
            $listEquipes = [];
            return [ 'listEquipes' =>$listEquipes,
                'choix' => $choix,
                'edition' => $edition,
                'titre' => $titre,
                'categorie' => $categorie,
                'parrain'=>$parrain,
                'photoparrain'=>$photoparrain,
                'titreparrain'=>$titreparrain,
                'affiche'=>$affiche

            ];
        }
    }

}
