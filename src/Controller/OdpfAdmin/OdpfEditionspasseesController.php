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
        $listeFichiers=$this->em->getRepository('App:Odpf\OdpfMemoires')->findBy(['equipe'=>$equipe]);
        $photos=$this->em->getRepository('App:Photos');
        $choix='equipepassee';
        $tab=$createArray->getArray($choix);
        $tab['equipe']=$equipe;
        $tab['memoire']=$listeFichiers;
        $tab['photos']=$photos;
        return $this->render('core/odpf-editions-passees-equipe.html.twig', $tab);
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
