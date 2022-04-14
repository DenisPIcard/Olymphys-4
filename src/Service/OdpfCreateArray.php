<?php

namespace App\Service;

use App\Entity\Odpf\OdpfArticle;
use App\Entity\Odpf\OdpfEditionsPassees;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class OdpfCreateArray
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

//dd($choix);
        $article = $repo->findOneBy(['choix' => $choix]);
        $categorie = $article->getCategorie();
        $texte = $article->getTexte();
        $titre = $article->getTitre();
        $titre_objectifs = $article->getTitreObjectifs();
        $texte_objectifs = $article->getTexteObjectifs();
        $image = $article->getImage();
        $alt_image = $article->getAltImage();
        $descr_image = $article->getDescrImage();
        //l'édtion en cours est considérée comme édition passée
        $editionpassee=$this->em->getRepository('App:Odpf\OdpfEditionsPassees')->findOneBy(['edition'=>$edition->getEd()]);
        $photoparrain='odpf-archives/'.$editionpassee->getEdition().'/parrain/'.$editionpassee->getPhotoParrain();
        $parrain=$editionpassee->getNomParrain();
        $lienparrain=$editionpassee->getLienparrain();
        $titreparrain=$editionpassee->getTitreParrain();
        $affiche='odpf-archives/'.$editionpassee->getEdition().'/affiche/'.$editionpassee->getAffiche();

        $tab = ['choix' => $choix,
            'article' => $article,
            'categorie' => $categorie,
            'titre' => $titre,
            'texte' => $texte,
            'titre_objectifs' => $titre_objectifs,
            'texte_objectifs' => $texte_objectifs,
            'image' => $image,
            'alt_image' => $alt_image,
            'descr_image' => $descr_image,
            'edition' => $edition,
            'parrain'=>$parrain,
            'photoparrain'=>$photoparrain,
            'titreparrain'=>$titreparrain,
            'lienparrain'=>$lienparrain,
            'affiche'=>$affiche];
        // dd($tab);
        return ($tab);
    }
}
