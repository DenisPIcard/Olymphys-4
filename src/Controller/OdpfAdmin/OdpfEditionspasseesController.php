<?php

namespace App\Controller\OdpfAdmin;

use App\Entity\Odpf\OdpfEditionsPassees;
use App\Service\OdpfCreateArray;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OdpfEditionspasseesController extends AbstractController
{
    private EntityManagerInterface $em;
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $em)
    {
        $this->requestStack = $requestStack;
        $this->em = $em;
    }
    /**
     * @Route("/odpf/editionspassees/equipe,{id}", name="odpf_editionspassees_equipe")
     */
    public function equipe($id,OdpfCreateArray $createArray): Response
    {   $equipe=$this->em->getRepository('App:Odpf\OdpfEquipesPassees')->findOneBy(['id'=>$id]);
        $listeFichiers=$this->em->getRepository('App:Odpf\OdpfFichierspasses')->findBy(['equipepassee'=>$equipe]);

        $photos=$this->em->getRepository('App:Photos')->findBy(['equipepassee'=>$equipe]);


        $choix='equipepassee';
        $tab=$createArray->getArray($choix);
        $tab['equipe']=$equipe;
        $tab['texte']=$this->createTextEquipe($equipe);
        $tab['memoires']=$listeFichiers;
        $tab['photos']=$photos;
       // dd($tab);
        return $this->render('core/odpf-editions-passees-equipe.html.twig', $tab);
    }
    /**
     * @Route("/odpf/editionspassees/editions", name="odpf_editionspassees_editions")
     */
    public function editions(OdpfCreateArray $createArray): Response
    {
        $editions=$this->getDoctrine()->getRepository(OdpfEditionsPassees::class)->createQueryBuilder('e')
            ->where('e.edition !=:lim')
            ->setParameter('lim',$this->requestStack->getSession()->get('edition')->getEd())
            ->getQuery()->getResult();;

        $idEdition=$_REQUEST['sel'];
        $editionAffichee =$this->getDoctrine()->getRepository('App:Odpf\OdpfEditionsPassees')->findOneBy(['id'=>$idEdition]);
        $choix='edition'.$editionAffichee->getEdition();
        $tab=$createArray->getArray($choix);
        $tab['edition_affichee']=$editionAffichee;
        $tab['editions']=$editions;

        return $this->render('core/odpf-pages-editions.html.twig', $tab);
    }
    public function createTextEquipe($equipe):string
    {
       $texte= '
                <table>
                <thead>
                <tr>
                    <th colspan="2"><h3>'.$equipe->getTitreProjet().'</h3></th>
               </tr>
               </thead>
                <tr>
                    <td colspan="2">Lycée '.$equipe->getLycee(). ' de '.$equipe->getVille().', académie de '.$equipe->getAcademie().'</td>
                </tr>
                
               <tr>
                    <td colspan="2"><b> Professeur(s) :  </b></td>
                    </tr>
                    <tr>
                    <td>'. $equipe->getProfs().'</td>
               </tr>
               <tr>
                    <td> <b>Elèves : </b></td>
               </tr>
               <tr>     
                    <td>'. $equipe->getEleves().'</td>
               </tr>
    
               </table>';
       if($equipe->getSelectionnee()==true){
           $texte=$texte.'<b>Sélectionnée pour le concours national</b><br>';
       }
       $memoires=$this->em->getRepository('App:Odpf\OdpfFichierspasses')->findBy(['equipepassee'=>$equipe]);

       foreach($memoires as $fichier){
           $fichier->getTypefichier()==1?$typefichier=0: $typefichier=$fichier->getTypefichier();



           $texte=$texte.'<a href="/../odpf-archives/'.$equipe->getEdition()->getEdition().'/fichiers/'.$this->getParameter('type_fichier')[$typefichier].'/'.$fichier->getNomfichier().'">'.$this->getParameter('type_fichier_lit')[$typefichier].'</a>, ';

       }


        return $texte;

    }

}
