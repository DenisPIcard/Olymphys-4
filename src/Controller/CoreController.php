<?php
// src/Controller/CoreController.php
namespace App\Controller;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
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
    public function index()
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
            $dateconnect = new \datetime('now');
            if ($dateconnect > $datecia) {
                $concours = 'national';
            }
            if (($dateconnect <= $datecia)) {
                $concours = 'interacadémique';
            }
            $datelimphotoscia = date_create();
            $datelimphotoscn = date_create();
            $datelimdiaporama = new \DateTime($this->requestStack->getSession()->get('edition')->getConcourscn()->format('Y-m-d'));
            $p = new \DateInterval('P7D');
            $datelimlivredor = new \DateTime($this->requestStack->getSession()->get('edition')->getConcourscn()->format('Y-m-d'));

            $datelivredor = new \DateTime($this->requestStack->getSession()->get('edition')->getConcourscn()->format('Y-m-d') . '00:00:00');
            $datelimlivredoreleve = new \DateTime($this->requestStack->getSession()->get('edition')->getConcourscn()->format('Y-m-d') . '18:00:00');
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
            $this->requestStack->getSession()->set('dateclotureinscription', new \DateTime($this->requestStack->getSession()->get('edition')->getConcourscn()->format('Y-m-d H:i:s')));

        }

        if ($this->requestStack->getSession()->get('resetpwd') == true) {

            return $this->redirectToRoute('forgotten_password');

        }
        if (($this->requestStack->getSession()->get('resetpwd') == false) or ($this->requestStack->getSession()->get('resetpwd') == null)) {
            return $this->render('core/index.html.twig');
        }
    }

    /**
     * @Route("/core/inscriptionscn", name="inscriptionscn")
     *
     */
    public function inscriptionscn(): \Symfony\Component\HttpFoundation\Response
    {
        return $this->render('core/inscriptions_cn.html.twig');

    }

}
