<?php
// src/Controller/CoreController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints\DateTime;

use App\Entity\User;
use App\Entity\Edition;  

class CoreController extends AbstractController
{    
    private $session;
   
    public function __construct(SessionInterface $session)
        {
            $this->session = $session;
        }
        
   
    
    /**
     * @Route("/", name="core_home")
     */
  public function index()
  {  
     
     $user=$this->getUser();
     $repositoryEdition = $this->getDoctrine()->getRepository('App:Edition');
   

      $edition=$repositoryEdition->findOneBy([], ['id' => 'desc']);
     $this->session->set('edition', $edition); 
    if (null != $user)
    {    
    $datelimcia = $edition->getDatelimcia();
    $datelimnat= $edition->getDatelimnat(); 
    $datecia= $edition->getConcourscia();
    $datecn= $edition->getConcourscn();
    $dateouverturesite= $edition->getDateouverturesite();
    $dateconnect= new \datetime('now');
      if ($dateconnect>$datecia) {
        $concours='national';
   }
    if (($dateconnect<=$datecia)) {
        $concours= 'interacadémique';
    }
     $datelimphotoscia=date_create();
     $datelimphotoscn=date_create();
      $datelimdiaporama=new \DateTime( $this->session->get('edition')->getConcourscn()->format('Y-m-d'));
     $p=new \DateInterval('P7D');
     $datelimlivredor=new \DateTime( $this->session->get('edition')->getConcourscn()->format('Y-m-d'));
     
     $datelivredor=new \DateTime( $this->session->get('edition')->getConcourscn()->format('Y-m-d').'00:00:00');
     $datelimlivredoreleve=new \DateTime( $this->session->get('edition')->getConcourscn()->format('Y-m-d').'18:00:00');
     date_date_set($datelimphotoscia,$edition->getconcourscia()->format('Y'),$edition->getconcourscia()->format('m'),$edition->getconcourscia()->format('d')+17);
     date_date_set($datelimphotoscn,$edition->getconcourscn()->format('Y'),$edition->getconcourscn()->format('m'),$edition->getconcourscn()->format('d')+30);
     date_date_set($datelivredor,$edition->getconcourscn()->format('Y'),$edition->getconcourscn()->format('m'),$edition->getconcourscn()->format('d')-1 );
     date_date_set($datelimdiaporama,$edition->getconcourscn()->format('Y'),$edition->getconcourscn()->format('m'),$edition->getconcourscn()->format('d')-7 );
     date_date_set($datelimlivredor,$edition->getconcourscn()->format('Y'),$edition->getconcourscn()->format('m'),$edition->getconcourscn()->format('d')+8 );
     $this->session->set('concours', $concours);   
     $this->session->set('datelimphotoscia', $datelimphotoscia);
     $this->session->set('datelimphotoscn', $datelimphotoscn);
     $this->session->set('datelivredor', $datelivredor);
     $this->session->set('datelimlivredor', $datelimlivredor);
     $this->session->set('datelimlivredoreleve', $datelimlivredoreleve);
     $this->session->set('datelimdiaporama', $datelimdiaporama);
     $this->session->set('dateclotureinscription', new \DateTime( $this->session->get('edition')->getConcourscn()->format('Y-m-d H:i:s')));
  
    }
   
    if($this->session->get('resetpwd')==true){

        return $this->redirectToRoute('forgotten_password');
        
    }
     if(($this->session->get('resetpwd')==false) or ($this->session->get('resetpwd')==null))
     {
             return $this->render('core/index.html.twig');
    }
  }
    

}
