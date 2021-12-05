<?php
// src/Controller/CoreController.php
namespace App\Controller;

use App\Entity\Edition;
use App\Entity\OdpfArticle;
use App\Service\OdpfCreateArray;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CoreController extends AbstractController
{
    private SessionInterface $session;

    public function __construct(SessionInterface $session){
        $this->session = $session;
    }

    /**
     * @Route("/", name="core_home")
     */
    public function accueil()
    {
        $user = $this->getUser();
        $repositoryEdition = $this->getDoctrine()->getRepository(Edition::class);
        $edition = $repositoryEdition->findOneBy([], ['id' => 'desc']);
        $this->session->set('edition', $edition);
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
            $datelimdiaporama = new \DateTime($this->session->get('edition')->getConcourscn()->format('Y-m-d'));
            $datelimlivredor = new \DateTime($this->session->get('edition')->getConcourscn()->format('Y-m-d'));

            $datelivredor = new \DateTime($this->session->get('edition')->getConcourscn()->format('Y-m-d') . '00:00:00');
            $datelimlivredoreleve = new \DateTime($this->session->get('edition')->getConcourscn()->format('Y-m-d') . '18:00:00');
            date_date_set($datelimphotoscia, $edition->getconcourscia()->format('Y'), $edition->getconcourscia()->format('m'), $edition->getconcourscia()->format('d') + 17);
            date_date_set($datelimphotoscn, $edition->getconcourscn()->format('Y'), $edition->getconcourscn()->format('m'), $edition->getconcourscn()->format('d') + 30);
            date_date_set($datelivredor, $edition->getconcourscn()->format('Y'), $edition->getconcourscn()->format('m'), $edition->getconcourscn()->format('d') - 1);
            date_date_set($datelimdiaporama, $edition->getconcourscn()->format('Y'), $edition->getconcourscn()->format('m'), $edition->getconcourscn()->format('d') - 7);
            date_date_set($datelimlivredor, $edition->getconcourscn()->format('Y'), $edition->getconcourscn()->format('m'), $edition->getconcourscn()->format('d') + 8);
            $this->session->set('concours', $concours);
            $this->session->set('datelimphotoscia', $datelimphotoscia);
            $this->session->set('datelimphotoscn', $datelimphotoscn);
            $this->session->set('datelivredor', $datelivredor);
            $this->session->set('datelimlivredor', $datelimlivredor);
            $this->session->set('datelimlivredoreleve', $datelimlivredoreleve);
            $this->session->set('datelimdiaporama', $datelimdiaporama);
            $this->session->set('dateclotureinscription', new \DateTime($this->session->get('edition')->getConcourscn()->format('Y-m-d H:i:s')));

        }
        return $this->render('core/odpf-accueil.html.twig');
    }

    /**
     * @Route("/core/pages,{choix}", name="core_pages")
     */
    public function pages(Request $request,$choix,OdpfCreateArray $OdpfCreateArray ): \Symfony\Component\HttpFoundation\Response
    {
        $tab=$OdpfCreateArray->getArray($choix);
        //dd($tab);
        return $this->render('core/odpf-pages.html.twig', $tab);
    }
}
    


