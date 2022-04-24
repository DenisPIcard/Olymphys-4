<?php
namespace App\Service;

use App\Entity\Odpf\OdpfArticle;
use App\Entity\Odpf\OdpfEquipesPassees;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;



class OdpfRempliEquipesPassees
{
    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine){
        $this->doctrine=$doctrine;


    }
    public function OdpfRempliEquipePassee($equipe)
    {

        $edition=$equipe->getEdition();
        $repositoryEquipesPassees=$this->doctrine->getRepository('App:Odpf\OdpfEquipesPassees');
        $repositoryEditionsPassees=$this->doctrine->getRepository('App:Odpf\OdpfEditionsPassees');
        $repositoryEleves=$this->doctrine->getRepository('App:Elevesinter');
        $editionPassee = $repositoryEditionsPassees->findOneBy(['edition' => $edition->getEd()]);
        $em=$this->doctrine->getManager();
        $OdpfEquipepassee = $repositoryEquipesPassees->createQueryBuilder('e')
            ->where('e.numero =:numero')
            ->andWhere('e.edition= :edition')
            ->setParameters(['numero'=>$equipe->getNumero(), 'edition' => $editionPassee])
            ->getQuery()->getOneOrNullResult();

        if ($OdpfEquipepassee === null) {
            $OdpfEquipepassee = new OdpfEquipesPassees();
        }
        $OdpfEquipepassee->setEdition($editionPassee);
        $OdpfEquipepassee->setNumero($equipe->getNumero());
      //dd($OdpfEquipepassee, $equipe);
        if ($equipe->getRneId() != null) {

           // $OdpfEquipepassee->setLettre($equipe->getLettre());
            $OdpfEquipepassee->setLycee($equipe->getRneId()->getNom());
            $OdpfEquipepassee->setVille($equipe->getRneId()->getCommune());
            $OdpfEquipepassee->setAcademie($equipe->getLyceeAcademie());
            $nomsProfs1 = ucfirst($equipe->getPrenomProf1()) . ' ' . strtoupper($equipe->getNomProf1());
            $equipe->getIdProf2() != null ? $nomsProfs = $nomsProfs1 . ', ' . $equipe->getPrenomProf2() . ' ' . $equipe->getNomProf2() : $nomsProfs = $nomsProfs1;
            $OdpfEquipepassee->setProfs($nomsProfs);
            $listeEleves = $repositoryEleves->findBy(['equipe' => $equipe]);
            $nomsEleves = '';
            foreach ($listeEleves as $eleve) {
                $nomsEleves = $nomsEleves . ucfirst($eleve->getPrenom()) . ' ' . $eleve->getNom() . ', ';
            }
            $OdpfEquipepassee->setEleves($nomsEleves);
        }
        if ($OdpfEquipepassee->getNumero())
        {
            $OdpfEquipepassee->setTitreProjet($equipe->getTitreProjet());
            $OdpfEquipepassee->setSelectionnee($equipe->getSelectionnee());
            $editionPassee->addOdpfEquipesPassee($OdpfEquipepassee);
            $em->persist($OdpfEquipepassee);
            $em->flush();
        }

    }





}