<?php
// src/Controller/CoreController.php
namespace App\Controller;

use App\Entity\Edition;
use App\Entity\Odpf\OdpfEditionsPassees;
use App\Service\OdpfCreateArray;
use App\Service\OdpfListeEquipes;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CoreController extends AbstractController
{
    private RequestStack $requestStack;
    private EntityManagerInterface $em;

    public function __construct(RequestStack $requestStack,EntityManagerInterface $em)
    {
        $this->requestStack = $requestStack;
        $this->em=$em;
    }

    /**
     * @Route("/", name="core_home")
     */
    public function accueil()
    {
        $user = $this->getUser();
        $repositoryEdition = $this->getDoctrine()->getRepository(Edition::class);
        $edition = $repositoryEdition->findOneBy([], ['id' => 'desc']);
        $this->requestStack->getSession()->set('edition', $edition);
        if (null != $user) {
            $datecia = $edition->getConcourscia();
            $dateconnect = new \datetime('now');
            if ($dateconnect > $datecia) {
                $concours = 'national';
            }
            if (($dateconnect <= $datecia)) {
                $concours = 'interacadémique';
            }
            $datelimphotoscia = date_create();
            $datelimphotoscn = date_create();
            $datelimdiaporama = new \DateTime( $this->requestStack->getSession()->get('edition')->getConcourscn()->format('Y-m-d'));
            $datelimlivredor = new \DateTime( $this->requestStack->getSession()->get('edition')->getConcourscn()->format('Y-m-d'));

            $datelivredor = new \DateTime( $this->requestStack->getSession()->get('edition')->getConcourscn()->format('Y-m-d') . '00:00:00');
            $datelimlivredoreleve = new \DateTime( $this->requestStack->getSession()->get('edition')->getConcourscn()->format('Y-m-d') . '18:00:00');
            date_date_set($datelimphotoscia, $edition->getconcourscia()->format('Y'), $edition->getconcourscia()->format('m'), $edition->getconcourscia()->format('d') + 17);
            date_date_set($datelimphotoscn, $edition->getconcourscn()->format('Y'), $edition->getconcourscn()->format('m'), $edition->getconcourscn()->format('d') + 30);
            date_date_set($datelivredor, $edition->getconcourscn()->format('Y'), $edition->getconcourscn()->format('m'), $edition->getconcourscn()->format('d') - 1);
            date_date_set($datelimdiaporama, $edition->getconcourscn()->format('Y'), $edition->getconcourscn()->format('m'), $edition->getconcourscn()->format('d') - 7);
            date_date_set($datelimlivredor, $edition->getconcourscn()->format('Y'), $edition->getconcourscn()->format('m'), $edition->getconcourscn()->format('d') + 8);
             $this->requestStack->getSession()->set('concours', $concours);
             $this->requestStack->getSession()->set('datelimphotoscia', $datelimphotoscia);
             $this->requestStack->getSession()->set('datelimphotoscn', $datelimphotoscn);
             $this->requestStack->getSession()->set('datelivredor', $datelivredor);
             $this->requestStack->getSession()->set('datelimlivredor', $datelimlivredor);
             $this->requestStack->getSession()->set('datelimlivredoreleve', $datelimlivredoreleve);
             $this->requestStack->getSession()->set('datelimdiaporama', $datelimdiaporama);
             $this->requestStack->getSession()->set('dateclotureinscription', new \DateTime( $this->requestStack->getSession()->get('edition')->getConcourscn()->format('Y-m-d H:i:s')));

        }
        return $this->render('core/odpf-accueil.html.twig');
    }

    /**
     * @Route("/core/pages,{choix}", name="core_pages")
     */
    public function pages(Request $request, $choix, OdpfCreateArray $OdpfCreateArray, OdpfListeEquipes $OdpfListeEquipes): \Symfony\Component\HttpFoundation\Response
    {
        if (($choix != 'les_equipes')and($choix!='editions')) {
            $tab = $OdpfCreateArray->getArray($choix);
            //dd($tab);
        }
        elseif($choix=='les_equipes') {
            $tab = $OdpfListeEquipes->getArray($choix);
            //dd($tab);
        }
        elseif($choix=='editions') {
            $editions=$this->em->getRepository(OdpfEditionsPassees::class)->findAll();
            $editionaffichee=$this->em->getRepository(OdpfEditionsPassees::class)->findOneBy(['edition'=>$this->requestStack->getSession()->get('edition')->getEd()-1]);//C'est l'édition précédente qui est affichée
            $choix='edition'.$this->em->getRepository('App:Odpf\OdpfEditionsPassees')
                    ->findOneBy(['edition'=>$editionaffichee->getEdition()])->getEdition();
            $tab = $OdpfCreateArray->getArray($choix);
            $tab['edition_affichee']=$editionaffichee;
            $tab['editions']=$editions;
            return $this->render('core/odpf-pages-editions.html.twig', $tab);
            //dd($tab);
        }
        elseif($choix='equipepassee'){



        }
        return $this->render('core/odpf-pages.html.twig', $tab);
    }
}