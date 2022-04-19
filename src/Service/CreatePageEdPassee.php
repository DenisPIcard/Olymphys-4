<?php

namespace App\Service;


use App\Entity\Odpf\OdpfArticle;
use App\Entity\Odpf\OdpfCategorie;
use App\Entity\Odpf\OdpfEditionsPassees;
use Doctrine\ORM\EntityManagerInterface;

class CreatePageEdPassee
{   private EntityManagerInterface $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;

    }

    public function create(OdpfEditionsPassees $editionsPassees): OdpfArticle
    {   $repositoryOdpfArticles=$this->em->getRepository('App:Odpf\OdpfArticle');
        if ($repositoryOdpfArticles->findOneBy(['titre'=>$editionsPassees->getEdition().'e edition'])==null) {
        $article = new OdpfArticle();
        }
        else{
         $article=$repositoryOdpfArticles->findOneBy(['titre'=>$editionsPassees->getEdition().'e edition']);

        }

        $listeEquipes = $this->em->getRepository('App:Odpf\OdpfEquipesPassees')->createQueryBuilder('e')
            ->select('e')
            ->andWhere('e.edition =:edition')
            ->andWhere('e.numero <:numero')
            ->setParameters(['edition'=>$editionsPassees,'numero'=>100])
            ->addOrderBy('e.academie','DESC')
            ->getQuery()->getResult();
        $article->setTitre($editionsPassees->getEdition() . 'e edition');
        $article->setChoix('edition'.$editionsPassees->getEdition());
        $ed=$editionsPassees->getEdition();
        $dateCia=$editionsPassees->getDateCia();

        $texte = '<p>Pour la ' . $editionsPassees->getEdition() . '<sup>e</sup> édition des Olympiades de Physique France, les inscriptions ont été ouvertes du ' . $editionsPassees->getDateinscription() . '.<br>

 Les concours intercadémiques ont eu lieu le ' . $editionsPassees->getDateCia() . '.<br>

Le compte rendu des Concours interacadémiques.<br>

La galerie des concours Interacadémiques.</p>
<p>Le concours national a eu lieu à ' . $editionsPassees->getLieu() . ' le ' . $editionsPassees->getDateCn() . '
 Le parrain de cette ' . $editionsPassees->getPseudo() . '<sup>e</sup> édition était ' . $editionsPassees->getNomParrain() . ', ' . $editionsPassees->getTitreParrain() . '.<br>

Le palmarès.<br>

La galerie du concours national.</p>
Liste des équipes
<ul>';
        foreach ($listeEquipes as $equipe) {
            $texte = $texte . '<li><a href="/../odpf/editionspassees/equipe,' . $equipe->getId() . '" >'. $equipe->getTitreProjet() . '</a>, lycée ' . $equipe->getLycee() . ', ' . $equipe->getVille() . '</li>';

        }
        $texte = $texte . '</ul>';
        $article->setTexte($texte);
        $categorie=$this->em->getRepository(odpfCategorie::class)->findOneBy(['id'=>4]);
        $article->setCategorie($categorie);
        $article->setTitreObjectifs('Retrouvez aussi :');
        return $article;
    }

}