<?php
// src/Controller/CoreController.php
namespace App\Controller;


use App\Entity\Edition;
use App\Service\OdpfCreateArray;
use App\Service\OdpfListeEquipes;
use DateInterval;
use datetime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CoreController extends AbstractController
{
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }


    /**
     * @Route("/", name="core_home")
     * @throws Exception
     */
    public function accueil()
    {

        $user = $this->getUser();

        $repositoryEdition = $this->getDoctrine()->getRepository('App:Edition');


        $edition = $repositoryEdition->findOneBy([], ['id' => 'desc']);
        $this->requestStack->getSession()->set('edition', $edition);
        if (null != $user) {
            $datelimcia = $edition->getDatelimcia();
            $datelimnat = $edition->getDatelimnat();
            $datecia = $edition->getConcourscia();
            $datecn = $edition->getConcourscn();
            $dateouverturesite = $edition->getDateouverturesite();
            $dateconnect = new datetime('now');
            if ($dateconnect > $datecia) {
                $concours = 'national';
            }
            if (($dateconnect <= $datecia)) {
                $concours = 'interacadémique';
            }
            $datelimphotoscia = date_create();
            $datelimphotoscn = date_create();
            $datelimdiaporama = new DateTime($this->requestStack->getSession()->get('edition')->getConcourscn()->format('Y-m-d'));
            $p = new DateInterval('P7D');
            $datelimlivredor = new DateTime($this->requestStack->getSession()->get('edition')->getConcourscn()->format('Y-m-d'));

            $datelivredor = new DateTime($this->requestStack->getSession()->get('edition')->getConcourscn()->format('Y-m-d') . '00:00:00');
            $datelimlivredoreleve = new DateTime($this->requestStack->getSession()->get('edition')->getConcourscn()->format('Y-m-d') . '18:00:00');
            date_date_set($datelimphotoscia, $edition->getconcourscia()->format('Y'), $edition->getconcourscia()->format('m'), $edition->getconcourscia()->format('d') + 30);
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
            $this->requestStack->getSession()->set('dateclotureinscription', new DateTime($this->requestStack->getSession()->get('edition')->getConcourscn()->format('Y-m-d H:i:s')));

        }

        if ($this->requestStack->getSession()->get('resetpwd') == true) {

            return $this->redirectToRoute('forgotten_password');

        }
        if (($this->requestStack->getSession()->get('resetpwd') == false) or ($this->requestStack->getSession()->get('resetpwd') == null)) {
            return $this->render('core/odpf-accueil.html.twig');
        }
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
            $editions=$this->getDoctrine()->getRepository(OdpfEditionsPassees::class)->createQueryBuilder('e')
                ->andWhere('e.edition !=:lim')
                ->setParameter('lim',$this->requestStack->getSession()->get('edition')->getEd())
                ->getQuery()->getResult();
            $editionaffichee=$this->em->getRepository(OdpfEditionsPassees::class)->findOneBy(['edition'=>$this->requestStack->getSession()->get('edition')->getEd()-1]);//C'est l'édition précédente qui est affichée
            $choix='edition'.$this->em->getRepository('App:Odpf\OdpfEditionsPassees')
                    ->findOneBy(['edition'=>$editionaffichee->getEdition()])->getEdition();
            $tab = $OdpfCreateArray->getArray($choix);
            $tab['edition_affichee']=$editionaffichee;
            $tab['editions']=$editions;
            return $this->render('core/odpf-pages-editions.html.twig', $tab);
            //dd($tab);
        }

        return $this->render('core/odpf-pages.html.twig', $tab);
    }
    }
