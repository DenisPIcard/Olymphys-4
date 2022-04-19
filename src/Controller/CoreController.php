<?php
// src/Controller/CoreController.php
namespace App\Controller;

use App\Entity\Odpf\OdpfArticle;
use App\Entity\Odpf\OdpfLogos;
use App\Entity\Odpf\OdpfEditionsPassees;
use App\Entity\Odpf\OdpfPartenaires;
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
use Doctrine\Persistence\ManagerRegistry;

class CoreController extends AbstractController
{
    private RequestStack $requestStack;
    private ManagerRegistry $doctrine;


    public function __construct(RequestStack $requestStack, ManagerRegistry $doctrine)
    {
        $this->requestStack = $requestStack;
        $this->doctrine = $doctrine;
    }


    /**
     * @Route("/", name="core_home")
     * @throws Exception
     */
    public function accueil(ManagerRegistry $doctrine)
    {

        $user = $this->getUser();

        $edition = $doctrine->getRepository('App:Edition')->findOneBy([], ['id' => 'desc']);
        $this->requestStack->getSession()->set('edition', $edition);

        if (null != $user) {
            $datecia = $edition->getConcourscia();
            $dateconnect = new datetime('now');
            if ($dateconnect > $datecia) {
                $concours = 'national';
            }
            if ($dateconnect <= $datecia) {
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
        $this->requestStack->getSession()->set('pageCourante', 1);

        $repo = $doctrine->getRepository(OdpfArticle::class);
        $tab=$repo->accueil_actus();

        if ($this->requestStack->getSession()->get('resetpwd') == true) {

            return $this->redirectToRoute('forgotten_password');

        }
        else {
            return $this->render('core/odpf-accueil.html.twig', $tab);
        }
    }

    /**
     * @Route("/core/pages,{choix}", name="core_pages")
     */
    public function pages(Request $request, $choix, ManagerRegistry $doctrine, OdpfCreateArray $OdpfCreateArray, OdpfListeEquipes $OdpfListeEquipes): Response
    {
        if ($choix == 'les_equipes') {
            $tab = $OdpfListeEquipes->getArray($choix);

        }
         elseif ($choix =='mecenes' or $choix =='donateurs') {
             $repo1 = $doctrine->getRepository(OdpfLogos::class);
             $tab = $repo1->logospartenaires($choix);
             $repo2 = $doctrine->getRepository(OdpfPartenaires::class);
             $tab['partenaires'] = $repo2->textespartenaires();

         }

        elseif($choix=='editions') {
            $editions=$doctrine->getRepository(OdpfEditionsPassees::class)->createQueryBuilder('e')
                ->andWhere('e.edition !=:lim')
                ->setParameter('lim',$this->requestStack->getSession()->get('edition')->getEd())
                ->getQuery()->getResult();
            $editionaffichee=$doctrine->getRepository(OdpfEditionsPassees::class)->findOneBy(['edition'=>$this->requestStack->getSession()->get('edition')->getEd()-1]);//C'est l'édition précédente qui est affichée
            $choice='editions';
            $choix='edition'.$doctrine->getRepository('App:Odpf\OdpfEditionsPassees')
                    ->findOneBy(['edition'=>$editionaffichee->getEdition()])->getEdition();
            $tab = $OdpfCreateArray->getArray($choix);
            $tab['edition_affichee']=$editionaffichee;
            $tab['editions']=$editions;
            $tab['choice']=$choice;
           // dd($tab);
            return $this->render('core/odpf-pages-editions.html.twig', $tab);

        }
        else{
            $tab = $OdpfCreateArray->getArray($choix);

        }

        return $this->render('core/odpf-pages.html.twig', $tab);
    }

    /**
     * @Route("/core/actus,{tourn}", name="core_actus")
     */
    public function odpf_actus(Request $request, $tourn,ManagerRegistry $doctrine): Response
    {
        $repo = $doctrine->getRepository(OdpfArticle::class);
        $categorie=$this->doctrine->getRepository(\App\Entity\Odpf\OdpfCategorie::class)->findOneBy(['categorie'=>'Les actus']);
        $tab=$repo->actuspaginees();

        $nbpages=$tab['nbpages'];
        $pageCourante=$this->requestStack->getSession()->get('pageCourante');

        switch ($tourn){
            case 'debut':
                $pageCourante=1;
                break;
            case 'prec':
                $pageCourante=$pageCourante-1;
                break;
            case 'suiv'  :
                $pageCourante +=1;
                break;
            case 'fin' :
                $pageCourante = $nbpages;
                break;

        }
        $tab['categorie']=$categorie;
        $tab['pageCourante']=$pageCourante;
        $this->requestStack->getSession()->set('pageCourante', $pageCourante);
        $actutil=$tab['affActus'];

        $affActus=$actutil[$pageCourante-1];

        $tab['affActus']=$affActus;
        //dd($tab);

        return $this->render('core/odpf-pages.html.twig', $tab);

    }



}
