<?php

namespace App\Controller\OdpfAdmin;

use App\Entity\Odpf\OdpfEditionsPassees;
use App\Service\OdpfCreateArray;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OdpfEditionspasseesController extends AbstractController
{
    /**
     * @Route("/odpf/editionspassees/equipe,{id}", name="editionspassees_equipe")
     */
    public function equipe($id): Response
    {   $equipe=$this->getDoctrine()->getRepository('App:Odpf\OdpfEquipesPassees')->findOneBy(['id'=>$id]);
        $listeFichiers=$this->getDoctrine()->getRepository('App:Odpf\OdpfMemoires')->find(['equipe']);
        $photos=$this->getDoctrine()->getRepository('App:Photos');

        return $this->render('odpf-editions-passees-equipe.html.twig', [
            'controller_name' => 'OdpfEditionspasseesController',
        ]);
    }
    /**
     * @Route("/odpf/editionspassees/editions", name="odpf_editionspassees_editions")
     */
    public function editions(OdpfCreateArray $createArray): Response
    {
        $editions=$this->getDoctrine()->getRepository(OdpfEditionsPassees::class)->findAll();
        $idEdition=$_REQUEST['sel'];
        $editionAffichee =$this->getDoctrine()->getRepository('App:Odpf\OdpfEditionsPassees')->findOneBy(['id'=>$idEdition]);
        $choix='edition'.$editionAffichee->getEdition();
        $tab=$createArray->getArray($choix);
        $tab['edition_affichee']=$editionAffichee;
        $tab['editions']=$editions;

        return $this->render('core/odpf-pages-editions.html.twig', $tab);
    }

}
