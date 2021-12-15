<?php
namespace App\Service;

use App\Entity\OdpfArticle;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class OdpfCreateArray
{
    private EntityManagerInterface $em;
    private SessionInterface $session;

    public function __construct(SessionInterface $session, EntityManagerInterface $em)
    {
        $this->session = $session;
        $this->em = $em;
    }
    public function getArray($choix): array
    {
        $edition = $this->session->get('edition');
        $repo=$this->em->getRepository(OdpfArticle::class);
        $article=$repo->findOneBy(['choix'=>$choix]);
        $idcategorie=$article->getIdCategorie();
        $texte=$article->getTexte();
        $titre=$article->getTitre();
        $titre_objectifs=$article->getTitreObjectifs();
        $texte_objectifs=$article->getTexteObjectifs();
        $image=$article->getImage();
        $alt_image=$article->getAltImage();
        $descr_image=$article->getDescrImage();
        $tab=[ 'choix'=>$choix,
            'article'=>$article,
            'idcategorie'=>$idcategorie,
            'titre'=>$titre,
            'texte'=>$texte,
            'titre_objectifs'=>$titre_objectifs,
            'texte_objectifs'=>$texte_objectifs,
            'image'=>$image,
            'alt_image'=>$alt_image,
            'descr_image'=>$descr_image,
            'edition'=>$edition ];
       // dd($tab);
        return($tab);
    }
}
