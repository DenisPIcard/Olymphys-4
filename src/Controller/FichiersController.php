<?php
namespace App\Controller ;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType ; 

use App\Service\Mailer;
use App\Form\NotesType ;
use App\Form\PhrasesType ;
use App\Form\EquipesType ;
use App\Form\JuresType ;
use App\Form\CadeauxType ;
use App\Form\ClassementType ;
use App\Form\PrixType ;
use App\Form\ToutfichiersType;
use App\Form\ConfirmType;
use App\Form\ListefichiersType;




use App\Entity\Equipes ;
use App\Entity\Eleves ;
use App\Entity\Elevesinter ;
use App\Entity\Edition ;
use App\Entity\Totalequipes ;
use App\Entity\Jures ;
use App\Entity\Notes ;
use App\Entity\Palmares;
use App\Entity\Visites ;
use App\Entity\Phrases ;
use App\Entity\Classement ;
use App\Entity\Prix ;
use App\Entity\Cadeaux ;
use App\Entity\Liaison ;
use App\Entity\Fichiersequipes;
use App\Entity\Equipesadmin;
use App\Entity\Centrescia;
use App\Entity\Videosequipes;
use App\Service\valid_fichiers;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

use Symfony\Component\Form\FormEvent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mime\MimeTypes;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\MimeTypeGuesserInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

//use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolation;

use Symfony\Component\Validator\Constraints as Assert;
//use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request ;
use Symfony\Component\HttpFoundation\RedirectResponse ;
use Symfony\Component\HttpFoundation\Response ;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\AcceptHeader;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\String\Slugger\SluggerInterface;

use Howtomakeaturn\PDFInfo\PDFInfo;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use ZipArchive;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

    
class FichiersController extends AbstractController
{
    //private $requestStack;
    private $requestStack;
    private $validator;
    private $parameterBag;
    public function __construct( RequestStack $requestStack,ValidatorInterface $validator, ParameterBagInterface $parameterBag)
        {
            $this->requestStack = $requestStack;;
            $this->validator=$validator;
            $this->parameterBag=$parameterBag;
        }
    
    
    
 /**
         * @Security("is_granted('ROLE_ORGACIA')")
         * 
         * @Route("/fichiers/choix_centre", name="fichiers_choix_centre")
         * 
         */           
public function choix_centre(Request $request) {
    $session=$this->requestStack->getSession();
    $repositoryEdition=$this->getDoctrine()
		->getManager()
		->getRepository('App:Edition');
    $repositoryCentres=$this->getDoctrine()
		->getManager()
		->getRepository('App:Centrescia');
    $repositoryEquipesAdmin=$this->getDoctrine()
		->getManager()
		->getRepository('App:Equipesadmin');
    $edition=$session->get('edition');
    $centres=$repositoryCentres->findAll();
    $equipes=$repositoryEquipesAdmin->findByEdition(['edition'=>$edition]);
    if ($equipes!=null){
         foreach($centres as $centre){
             foreach($equipes as $equipe){
             if ($centre==$equipe->getCentre()){
                $liste_centres[$centre->getCentre()]=$centre;
                 
             }
             
             }
         }
    }        
            
            //dd($liste_centres);      
    
    
    
   
     if(isset($liste_centres)) {
                   $content = $this
                 ->renderView('adminfichiers\choix_centre.html.twig', array(
                     'liste_centres'=>$liste_centres
                    )
                                );
        return new Response($content);  
     }
     else{
         $request->getSession()
                                     ->getFlashBag()
                                     ->add('info', 'Pas encore de centre attribué pour le  concours interacadémique de l\'édition '.$edition->getEd()) ;
                             return $this->redirectToRoute('core_home'); 
         
         
         
         
     }
    }
 
 /**
         * @Security("is_granted('ROLE_PROF')")
         * 
         * @Route("/fichiers/choix_equipe, {choix}", name="fichiers_choix_equipe")
         * 
         */           
public function choix_equipe(Request $request,$choix) {

    $session=$this->requestStack->getSession();
    $repositoryEquipesadmin= $this->getDoctrine()
		->getManager()
		->getRepository('App:Equipesadmin');
    $repositoryEdition= $this->getDoctrine()
		->getManager()
		->getRepository('App:Edition');
     $repositoryCentres= $this->getDoctrine()
		->getManager()
		->getRepository('App:Centrescia');   
     $repositoryEleves=$this->getDoctrine()
		->getManager()
		->getRepository('App:Elevesinter');
      $repositoryDocequipes= $this->getDoctrine()
                                 ->getManager()
                                 ->getRepository('App:Docequipes');
    $edition=$session->get('edition');
    $docequipes=$repositoryDocequipes->findAll();
    $centres=$repositoryCentres->findAll();
    $datelimcia = $edition->getDatelimcia();
    $datelimnat=$edition->getDatelimnat(); 
    $datecia=$edition->getConcourscia(); 
    $datecn=$edition->getConcourscn(); 
    $dateouverturesite=$edition->getDateouverturesite();
    $dateconnect= new \datetime('now');
    
    $user = $this->getUser();


    $id_user=$user->getId(); 
    $roles=$user->getRoles();
    $role=$roles[0];
    
         if($role=='ROLE_JURY'){
             $nom=$user->getUsername();
            
             $repositoryJures = $this->getDoctrine()
		->getManager()
		->getRepository('App:Jures');
                $jure=$repositoryJures->findOneBy(['iduser'=>$this->getUser()->getId()]);
		        $id_jure = $jure->getId();
               }
    $qb1 =$repositoryEquipesadmin->createQueryBuilder('t')
                             ->andWhere('t.selectionnee=:selectionnee')
                             ->setParameter('selectionnee', TRUE)
                             ->andWhere('t.lettre >:valeur')
                             ->andWhere('t.edition =:edition')
                             ->setParameter('edition', $edition)
                             ->setParameter('valeur','')
                             ->orderBy('t.lettre', 'ASC');
    
    
     $qb3 =$repositoryEquipesadmin->createQueryBuilder('t')
                             ->where('t.rneId =:rne')
                             ->andWhere('t.edition =:edition')
                             ->setParameter('edition', $edition)
                             ->setParameter('rne', $user->getRneId());

   if ($dateconnect>$datecia) {
        $phase='national';
       $qb3->orderBy('t.lettre', 'ASC');
   }
    if (($dateconnect<=$datecia)) {
        $phase= 'interacadémique';
        $qb3->orderBy('t.numero', 'ASC');
    }
             
         
     
    if (($choix=='liste_cn_comite') )  {
                    if (($role=='ROLE_COMITE') or ($role=='ROLE_JURY') or ($role=='ROLE_SUPER_ADMIN')){

                        $liste_equipes=$qb1->getQuery()->getResult();    
                        if($liste_equipes!=null) {
                           if(($role=='ROLE_COMITE') or ($role=='ROLE_SUPER_ADMIN')){

                        $content = $this
                                 ->renderView('adminfichiers\choix_equipe.html.twig', array(
                                     'liste_equipes'=>$liste_equipes,  'user'=>$user, 'phase'=>'national', 'role'=>$role,'choix'=>$choix
                                    )
                           );}
                           if($role=='ROLE_JURY'){
                        $content = $this
                                 ->renderView('adminfichiers\choix_equipe.html.twig', array(
                                     'liste_equipes'=>$liste_equipes,  'user'=>$user, 'phase'=>'national', 'role'=>$role,'choix'=>$choix,'jure'=>$jure)//Jure necessaire pour le titre 
                                       ); 
                           }
                           return new Response($content);  
                       } 
                        else{
                           $request->getSession()
                                ->getFlashBag()
                                ->add('info', 'Pas encore d\'équipe sélectionnée pour le concours national de la '.$edition->getEd().'e edition') ;
                        return $this->redirectToRoute('core_home');    
                        }
                    }    
    } 
    
    foreach($centres as $Centre){
              if ($Centre->getCentre()==$choix){
                                      $centre = $Centre;
              } 
    }
    
    
    
    
    
    if (isset($centre) or ($choix=='centre'))  { //pour le jurycia, comité, superadmin liste des équipes d'un centre
                            if (($role=='ROLE_COMITE') or ($role=='ROLE_JURY') or ($role=='ROLE_SUPER_ADMIN')or ($role=='ROLE_ORGACIA') or ($role=='ROLE_JURYCIA')){  
                                  if (!isset($centre)){
                                      $centre=$this->getUser()->getCentrecia();
                                  }
                                  
                                  $qb2 =$repositoryEquipesadmin->createQueryBuilder('t')
                                                  ->where('t.centre =:centre')
                                                  ->setParameter('centre', $centre)
                                                  ->andWhere('t.edition =:edition')
                                                  ->setParameter('edition', $edition)
                                                  ->orderBy('t.numero', 'ASC');
                             $liste_equipes=$qb2->getQuery()->getResult();  
                        
                             if($liste_equipes!=null) {

                             $content = $this
                                      ->renderView('adminfichiers\choix_equipe.html.twig', array(
                                          'liste_equipes'=>$liste_equipes,  'user'=>$user, 'phase'=>'interacadémique', 'role'=>$role,'choix'=>'liste_prof','centre'=>$centre->getCentre()
                                         )
                                                     );
                             return new Response($content);  

                             } 
                             if($liste_equipes==null) {
                                $request->getSession()
                                     ->getFlashBag()
                                     ->add('info', 'Pas encore d\'équipe pour le concours interacadémique de la '.$edition->getEd().'e edition') ;
                             return $this->redirectToRoute('core_home');    
                             }
                         }
    }
    

if (($choix=='liste_prof')) {

    if (($phase == 'interacadémique') or ($role == 'ROLE_ORGACIA')) {
        if ($role == 'ROLE_PROF') {
            $liste_equipes = $qb3->getQuery()->getResult();
            $rne_objet = $this->getDoctrine()->getManager()->getRepository('App:Rne')->find(['id' => $user->getRneId()]);

        }

        if ($role == 'ROLE_ORGACIA') {
            $centre = $this->getUser()->getCentrecia();

            $liste_equipes = $repositoryEquipesadmin->createQueryBuilder('t')
                ->where('t.centre =:centre')
                ->setParameter('centre', $centre)
                ->andWhere('t.edition =:edition')
                ->setParameter('edition', $edition)
                ->orderBy('t.numero', 'ASC')->getQuery()->getResult();
            $rne_objet = null;
        }


        if (($role != 'ROLE_ORGACIA') and ($role != 'ROLE_PROF')) {
            $liste_equipes = null;
            if ($dateconnect > $datecia) {
                /*$qb3->andWhere('t.selectionnee=:selectionnee')
                        ->setParameter('selectionnee', TRUE)
                        ->orderBy('t.lettre', 'ASC');    */
                $liste_equipes = $qb3->getQuery()->getResult();
                $rne_objet = null;
            }
        }
    }

        //if($liste_equipes!=null) {
        if ($phase=='national') {


            if ($role == 'ROLE_PROF') {
                $liste_equipes = //$qb3->andWhere('t.selectionnee = 1')
                    $qb3->getQuery()->getResult();
                $rne_objet = $this->getDoctrine()->getManager()->getRepository('App:Rne')->find(['id' => $user->getRneId()]);

            }
        }
        $content = $this
            ->renderView('adminfichiers\choix_equipe.html.twig', array(
                'liste_equipes' => $liste_equipes, 'phase' => $phase, 'user' => $user, 'choix' => $choix, 'role' => $role, 'doc_equipes' => $docequipes, 'rneObj' => $rne_objet
            ));
        return new Response($content);

        /*         }
       /*  else{
        $request->getSession()
             ->getFlashBag()
                ->add('info', 'Le site n\'est pas encore prêt pour une saisie des mémoires ou vous n\'avez pas d\'équipe inscrite pour le concours '. $phase.' de la '.$edition->getEd().'e edition') ;

        return $this->redirectToRoute('core_home');
            }*/


    }



   
  if (($choix=='deposer')) {//pour le dépôt des fichiers autres que les présentations
      
                                            if ($role=='ROLE_PROF') {
                                                
                                        if ($choix=='diaporama_jury')  {
                                            if ($dateconnect<=$datecia){
                                              $phase= 'interacadémique';

                                             $liste_equipes=$qb3->getQuery()->getResult();    
                                            }
                                            
                                          if (($dateconnect<=$datecn) and ($dateconnect>$datecia)){
                                              $phase= 'national';

                                              $qb3 ->andWhere('t.selectionnee=:selectionnee')
                                                      
                                                                  ->setParameter('selectionnee', TRUE);     
                                             $liste_equipes=$qb3->getQuery()->getResult();    
                                             
                                            }
                                        } 
                                        
                                        
                                        
                                        else
                                        {    
                                            
                                         if (($dateconnect>$datelimcia) and ($dateconnect<=$datelimnat)) {
                                             $phase='national';
                                              $qb3 ->andWhere('t.selectionnee=:selectionnee')
                                                      
                                                                  ->setParameter('selectionnee', TRUE);     
                                             $liste_equipes=$qb3->getQuery()->getResult();    
                                             }
                                         if (($dateconnect>$dateouverturesite) and ($dateconnect<=$datelimcia)) {
                                             $phase= 'interacadémique';

                                             $liste_equipes=$qb3->getQuery()->getResult();   
                                         }
                                       }
                                       
                                           if($liste_equipes!=null) {

                                             $content = $this
                                                      ->renderView('adminfichiers\choix_equipe.html.twig', array(
                                                          'liste_equipes'=>$liste_equipes, 'phase'=>$phase, 'user'=>$user,'choix'=>$choix,'role'=>$role
                                                         )
                                                                     );
                                             return new Response($content);  
                                             }   
                                         else{ 
                                             $request->getSession()
                                                     ->getFlashBag()
                                                     ->add('info', 'Le site n\'est pas encore prêt pour une saisie des mémoires ou vous n\'avez pas d\'équipe inscrite pour le concours '. $phase.' de la '.$edition->getEd().'e edition') ;
                                             return $this->redirectToRoute('core_home');   
                                             }
                                            }

                                             if( ($role=='ROLE_COMITE')  ){
                                                 
                                                 
                                                if (($dateconnect>$datelimcia) ) {
                                             $phase='national';
                                               $qb4 =$repositoryEquipesadmin->createQueryBuilder('t')
                                                                ->where('t.selectionnee=:selectionnee')
                                                                 ->setParameter('selectionnee',TRUE)
                                                                 ->andWhere('t.edition =:edition')
                                                                  ->setParameter('edition', $edition)
                                                                 ->andWhere('t.lettre>:valeur')
                                                                 ->setParameter('valeur', '')
                                                                 ->orderBy('t.lettre','ASC');
                                             $liste_equipes=$qb4->getQuery()->getResult();
                                             
                                           
                                                }
                                               if (($dateconnect>$dateouverturesite) and ($dateconnect<=$session->get('concourscn'))) {
                                             $phase= 'interacadémique';
                                             $qb4 =$repositoryEquipesadmin->createQueryBuilder('t')
                                                                  ->where('t.nomLycee>:vide')
                                                                 ->setParameter('vide','')
                                                                   ->andWhere('t.edition =:edition')
                                                                  ->setParameter('edition', $edition)
                                                                 ->orderBy('t.numero','ASC');
                                             $liste_equipes=$qb4->getQuery()->getResult();  
                                               }
                                                if($liste_equipes) {

                                             $content = $this
                                                      ->renderView('adminfichiers\choix_equipe.html.twig', array(
                                                          'liste_equipes'=>$liste_equipes, 'phase'=>$phase, 'user'=>$user,'choix'=>$choix,'role'=>$role
                                                         )
                                                                     );
                                             return new Response($content);  
                                             }   
                                         else{ 
                                             $request->getSession()
                                                     ->getFlashBag()
                                                     ->add('info', 'Le site n\'est pas encore prêt pour une saisie des mémoires ou vous n\'avez pas d\'équipes inscrite pour le concours '. $phase.' de la '.$edition->getEd().'e edition') ;
                                             return $this->redirectToRoute('core_home');   
                                         }
                                               
                                               
                                               
                                             }
                                                if (($role=='ROLE_ORGACIA') or ($role=='ROLE_JURYCIA') ) {

                                                    $centre=$user->getCentrecia()->getCentre();
                                                    $qb5= $repositoryEquipesadmin->createQueryBuilder('t')
                                                                  ->where('t.nomLycee>:vide')
                                                                 ->setParameter('vide','')
                                                                  ->andWhere('t.edition =:edition')
                                                                 ->setParameter('edition', $edition)
                                                                 ->orderBy('t.numero','ASC')
                                                                ->andWhere('t.centre =:centre')
                                                                ->setParameter('centre', $user->getCentrecia());
                                                    $liste_equipes=$qb5->getQuery()->getResult();  
                                                   // if ($dateconnect>$datecia){
                                                   //     return $this->redirectToRoute('core_home'); 
                                                        
                                                   // }
                                                



                                              if($liste_equipes) {

                                             $content = $this
                                                      ->renderView('adminfichiers\choix_equipe.html.twig', array(
                                                          'liste_equipes'=>$liste_equipes, 'phase'=>$phase, 'user'=>$user,'choix'=>$choix,'role'=>$role,'centre'=>$centre
                                                         )
                                                                     );
                                             return new Response($content);  
                                             }   
                                         else{ 
                                             $request->getSession()
                                                     ->getFlashBag()
                                                     ->add('info', 'Le site n\'est pas encore prêt pour une saisie des mémoires ou vous n\'avez pas d\'équipes inscrite pour le concours '. $phase.' de la '.$edition->getEd().'e edition') ;
                                             return $this->redirectToRoute('core_home');   
                                         }
                                             }
         }
        
 }

 


        /**
         * @Security("is_granted('ROLE_PROF')")
         * @var Symfony\Component\HttpFoundation\File\UploadedFile $file 
         * @Route("/fichiers/charge_fichiers, {infos}", name="fichiers_charge_fichiers")
         * 
         */         
public function  charge_fichiers(Request $request, $infos ,MailerInterface $mailer,ValidatorInterface $validator)
{
    $session=$this->requestStack->getSession();
    $repositoryFichiersequipes = $this->getDoctrine()
        ->getManager()
        ->getRepository('App:Fichiersequipes');
    $repositoryEquipesadmin = $this->getDoctrine()
        ->getManager()
        ->getRepository('App:Equipesadmin');
    $repositoryEdition = $this->getDoctrine()
        ->getManager()
        ->getRepository('App:Edition');
    $repositoryUser = $this->getDoctrine()
        ->getManager()
        ->getRepository('App:User');
    $repositoryEleve = $this->getDoctrine()
        ->getManager()
        ->getRepository('App:Elevesinter');
    $validFichier = new valid_fichiers($this->validator, $this->parameterBag, $this->requestStack);

   //dd($_SERVER);
    $info = explode('-', $infos);

    $id_equipe = $info[0];
    $phase = $info[1];
    $choix = $info[2];
    if ($choix == 0 or $choix == 1 or $choix == 2 or $choix == 7) {

            if (($session->get('edition')->getDatelimcia() < new \DateTime('now')) and ($session->get('concours')=='interacadémique')) {
                $this->addFlash('alert', 'La date limite de dépôt des fichiers est dépassée, veuillez contacter le comité!');
                return $this->redirectToRoute('fichiers_afficher_liste_fichiers_prof', [
                    'infos' => $infos,
                ]);


            }
        if (($session->get('edition')->getDatelimnat() < new \DateTime('now')) and ($session->get('concours')=='national')) {
            $this->addFlash('alert', 'La date limite de dépôt des fichiers est dépassée, veuillez contacter le comité!');
            return $this->redirectToRoute('fichiers_afficher_liste_fichiers_prof', [
                'infos' => $infos,
            ]);


        }


    }
    if (count($info) >= 5) {//pour les autorisations photos
            $id_citoyen = $info[3];


            if ($id_equipe != 'prof') {//autorisations photos des élèves
                $attrib = $info[4];
                $citoyen = $repositoryEleve->find(['id' => $id_citoyen]);
                $equipe = $repositoryEquipesadmin->find(['id' => $id_equipe]);
                $prof=false;
            } else {//autorisation photo des profs
                $citoyen = $repositoryUser->find(['id' => $id_citoyen]);
                $id_equipe=$info[4];
                $attrib=$info[5];
                $prof=true;
                $equipe=$repositoryEquipesadmin->findOneBy(['id'=>$id_equipe]);
            }
    }
    else {
            $equipe = $repositoryEquipesadmin->find(['id' => $id_equipe]);
            $attrib = $info[3];
            if ($attrib=='1') {//upload d'un fichier FichierID est fourni par la fenêtre modale
                $idfichier = $request->query->get('FichierID');

                $fichier = $repositoryFichiersequipes->findOneBy(['id' => $idfichier]);
                if ($fichier != null)
                {
                    $choix = $repositoryFichiersequipes->findOneBy(['id' => $idfichier])->getTypefichier();

                }
                else{//Cela indique que le fichier n'est pas valide car valid_fichier fait disparaître les paramètres de $request->query
                    $idfichier=$session->get('idFichier');

                    $fichier=$repositoryFichiersequipes->findOneBy(['id' => $idfichier]);
                    $choix=$fichier->getTypefichier();// nécessaire dans le cas d'un upload de fichier non valide, valid_fichier fait disparaître les paramètres de $request->query

                }
                if ($choix==6){
                    if ($request->query->get('FichierID')!=null) {
                        $citoyen = $repositoryFichiersequipes->findOneBy(['id' => $idfichier])->getEleve();//pour les élèves
                        if ($citoyen === null) {
                            $citoyen = $repositoryFichiersequipes->findOneBy(['id' => $idfichier])->getProf();//pour les profs

                        }

                    }
                    else{  //Cela indique que le fichier n'est pas valide car valid_fichier fait disparaître les paramètres de $request->query
                        $citoyen = $repositoryFichiersequipes->findOneBy(['id' => $idfichier])->getEleve();//pour les élèves
                        if ($citoyen === null) {
                            $citoyen = $repositoryFichiersequipes->findOneBy(['id' => $idfichier])->getProf();//pour les profs
                        }

                    }

                }
            }
    }

    

      $edition=$this->requestStack->getSession()->get('edition');

      $datelimnat=$edition->getDatelimnat();
   
      $dateconnect= new \datetime('now');
      
      $form1=$this->createForm(ToutfichiersType::class, ['choix'=>$choix]);
      if(isset($equipe)){
            $nom_equipe=$equipe->getTitreProjet();
            $lettre_equipe= $equipe->getLettre();

            $donnees_equipe=$lettre_equipe.' - '.$nom_equipe;

            if(!$lettre_equipe){
                $numero_equipe=$equipe->getNumero();
                $nom_equipe=$equipe->getTitreProjet();
                $donnees_equipe=$numero_equipe.' - '.$nom_equipe;
            }
      }
      else{
          $donnees_equipe= $citoyen->getPrenom().' '.$citoyen->getNom();
          
          
      }
      
    $form1->handleRequest($request);

    if ($form1->isSubmitted() && $form1->isValid()){
           
       /** @var UploadedFile $file */
          $file=$form1->get('fichier')->getData();

        $ext=$file->guessExtension();
        $num_type_fichier=$form1->get('choice')->getData();
        
       if (!isset($num_type_fichier)){//sert pour les mémoires et annexes
           
            $this->addFlash('alert', 'Sélectionner le type de fichier !');
                                       return $this->redirectToRoute('fichiers_charge_fichiers', [
                                           'infos' => $infos,
                                       ]);
       }
       $idFichier=null;
       if(isset($fichier)){
           $idFichier=$fichier->getId();
       }
       $violations=$validFichier->validation_fichiers($file,$num_type_fichier,$idFichier)['text'];
       if ($violations!=''){
           $request->getSession()
               ->getFlashBag()
               ->add('alert', $violations ) ;
           return $this->redirectToRoute('fichiers_charge_fichiers',array('infos'=>$infos));

       }
       $em=$this->getDoctrine()->getManager();
       $edition=$repositoryEdition->findOneBy([], ['id' => 'desc']);

       if ($attrib==0){

           $fichier=new Fichiersequipes();
           if ($num_type_fichier==6){//Vérification pour les autorisation photos prof, en cas de bug lors d'un dépôt le fichier existe mais n'est l'user n'est pas lié à lui : conflit clef unique
               $fichier=$repositoryFichiersequipes->createQueryBuilder('f')
                   ->andWhere('f.prof =:citoyen')
                   ->setParameter('citoyen',$citoyen)
                   ->getQuery()->getOneOrNullResult();
               if ($fichier != null) {
                   $idfichier = $fichier->getId();
                   $attrib = 1;
               }

           }
           $fichier=new Fichiersequipes();
       }
       if($attrib>0){
                    $fichier=$repositoryFichiersequipes->findOneBy(['id'=>$idfichier]);
                    $message = '';

       }
       $fichier->setFichierFile($file);

                if ($attrib==0) {

                    if ($session->get('concours') == 'national') { //on vérifie que le fichier cia existe et on écrase sans demande de confirmation ce fichier  par le fichier national  sauf les autorisations photos
                        if ($num_type_fichier < 6) {
                            try {
                                $fichier = $repositoryFichiersequipes->createQueryBuilder('f')
                                    ->where('f.equipe=:equipe')
                                    ->setParameter('equipe', $equipe)
                                    ->andWhere('f.typefichier =:type')
                                    ->setParameter('type', $num_type_fichier)
                                    ->andWhere('f.national =:valeur')
                                    ->setParameter('valeur', '0')
                                    ->getQuery()->getSingleResult();
                            } catch (\Exception $e) {// précaution pour éviter une erreur dans le cas du manque du fichier cia, ce qui arrive souvent pour les résumés, annexes, fiche sécurité,
                                $message = '';
                                $fichier = new Fichiersequipes();
                                $nouveau = true;
                            }
                            if (!isset($nouveau)) {
                                $message = 'Pour éviter les confusions, le fichier interacadémique n\'est plus accessible. ';
                            }
                        }
                        if ($num_type_fichier == 6) {
                            $fichier = new Fichiersequipes();

                        }
                    }

                    if ($session->get('concours') == 'interacadémique') {
                        $fichier = new Fichiersequipes();
                        $message = '';
                    }
                    $fichier->setTypefichier($num_type_fichier);
                    $fichier->setEdition($edition);
                    if (isset($equipe)) {
                        $fichier->setEquipe($equipe);
                    }
                    $fichier->setNational(0);


                    if ($phase == 'national') {
                        $fichier->setNational(1);
                    }

                    if ($num_type_fichier == 6) {
                        $fichier->setNomautorisation($citoyen->getNom() . '-' . $citoyen->getPrenom());
                        if ($prof==false) {
                            $fichier->setEleve($citoyen);
                        } else {

                            $fichier->setProf($citoyen);
                        }
                    }
                    $fichier->setFichierFile($file);
                }
                $em->persist($fichier);
                $em->flush();
                if ($num_type_fichier==6){
                               
                             $citoyen->setAutorisationphotos($fichier);
                             $em->persist($citoyen);
                             $em->flush();                             
                         }    
                            $nom_fichier = $fichier->getFichier();
                     
                    $request->getSession()
                            ->getFlashBag()
                            ->add('info', $message.'Votre fichier renommé selon : '.$nom_fichier.' a bien été déposé. Merci !') ;
               
                $user = $this->getUser();//Afin de rappeler le nom du professeur qui a envoyé le fichier dans le mail
                $type_fichier= $this->getParameter('type_fichier')[$num_type_fichier];
                
                $type_fichier= $this->getParameter('type_fichier_lit')[$num_type_fichier];
              
                if (isset($equipe)){
                 if ($phase != 'national'){
                   $info_equipe='L\'equipe '. $equipe->getInfoequipe();
                    }
                    
                    if ($phase == 'national'){
                   $info_equipe='L\'equipe '. $equipe->getInfoequipenat();
                    }
                }
                else{
                    
                     $info_equipe='prof '. $citoyen->getNomPrenom();
                }
               $this->MailConfirmation($mailer,$type_fichier,$info_equipe);
                
                return $this->redirectToRoute('fichiers_afficher_liste_fichiers_prof', array('infos'=>$equipe->getId().'-'.$session->get('concours').'-liste_prof'));     
                }


    
            if ($choix == '6'){
                 $content = $this
                             ->renderView('adminfichiers\charge_fichier_fichier.html.twig', array('form'=>$form1->createView(),'donnees_equipe'=>$donnees_equipe,'citoyen'=>$citoyen, 'choix'=>$choix,'infos'=>$infos));
            }
            else {
             $content = $this
                             ->renderView('adminfichiers\charge_fichier_fichier.html.twig', array('form'=>$form1->createView(),'donnees_equipe'=>$donnees_equipe, 'choix'=>$choix, 'infos'=>$infos ));
            }
            return new Response($content);                             
 }
 
 
public function MailConfirmation(MailerInterface $mailer, string $type_fichier, string $info_equipe){
  
    $email=(new Email())
                    ->from('info@olymphys.fr')
                    ->cc('webmestre3@olymphys.fr')
                    ->to('webmestre2@olymphys.fr');
    if ($type_fichier=='autorisation'){
        $email->cc('gilles.pauliat@institutoptique.fr');
    }
    $email->subject('Depot du '.$type_fichier.' de '.$info_equipe)
                    ->text($info_equipe.' a déposé un fichier : '.$type_fichier.'.');
                   
    $mailer->send($email);
   
 }


           /**
         * @Security("is_granted('ROLE_PROF')")
         * 
         * @Route("/fichiers/mon_espace", name="mon_espace")
         * 
         */          
public function mon_espace(Request $request ){

             $session=$this->requestStack->getSession();
             $user = $this->getUser();
             $id_user=$user->getId(); 
             $edition=$session->get('edition');
             $repositoryFichiersequipes= $this->getDoctrine()
                              ->getManager()
                              ->getRepository('App:Fichiersequipes');
              $repositoryEquipesadmin= $this->getDoctrine()
                              ->getManager()
                              ->getRepository('App:Equipesadmin');
              $qb3 =$repositoryEquipesadmin->createQueryBuilder('t')
                             ->where('t.idProf1=:professeur')
                             ->orwhere('t.idProf2=:professeur')
                             ->andWhere('t.edition =:edition')
                             ->setParameter('edition', $edition)
                             ->setParameter('professeur', $id_user);
              if ($this->requestStack->getSession()->get('concours')=='interacadémique'){
                             $qb3->orderBy('t.numero', 'ASC');
                  }
              if ($this->requestStack->getSession()->get('concours')=='national'){
                        $qb3->orderBy('t.lettre', 'ASC');
                    }

             $liste_equipes=$qb3->getQuery()->getResult();
                dd($liste_equipes);
             foreach($liste_equipes as $equipe){
                 
             $id_equipe=$equipe->getId();
             $qb1 =$repositoryFichiersequipes->createQueryBuilder('t')
                             ->LeftJoin('t.equipe', 'e')
                             ->Where('e.id=:id_equipe')
                              ->andWhere('e.edition =:edition')
                             ->setParameter('edition', $edition)
                             ->setParameter('id_equipe', $id_equipe)
                             ->addOrderBy('t.typefichier','ASC');
               $liste_fichiers[$id_equipe]=$qb1->getQuery()->getResult();
             }
               return $this->render('/adminfichiers/espace_prof.html.twig', array('liste_equipes'=>$liste_equipes,'liste_fichiers'=>$liste_fichiers));
    
    
            }
 
 
 
 
         
        /**
         * @Security("is_granted('ROLE_PROF')")
         * 
         * @Route("/fichiers/afficher_liste_fichiers_prof/,{infos}", name="fichiers_afficher_liste_fichiers_prof")
         * 
         */          
public function     afficher_liste_fichiers_prof(Request $request , $infos ){
    $session=$this->requestStack->getSession();
    $session->set('oldlisteEleves', null);
    $session->set('supr_eleve',null);

    $repositoryFichiersequipes= $this->getDoctrine()
                              ->getManager()
                              ->getRepository('App:Fichiersequipes');
    $repositoryVideosequipes= $this->getDoctrine()
                              ->getManager()
                              ->getRepository('App:Videosequipes');
    $repositoryEquipesadmin= $this->getDoctrine()
                                  ->getManager()
                                  ->getRepository('App:Equipesadmin');
     $repositoryUser= $this->getDoctrine()
                                  ->getManager()
                                  ->getRepository('App:User');
      $repositoryEdition= $this->getDoctrine()
                                 ->getManager()
                                 ->getRepository('App:Edition');
      $repositoryElevesinter= $this->getDoctrine()
                                 ->getManager()
                                 ->getRepository('App:Elevesinter');
     
    $Infos=explode('-',$infos);

    $id_equipe=$Infos[0];
    if ($id_equipe=='prof'){
        
        $id_equipe=$Infos[4];
    }
    $concours=$Infos[1];
    $choix=$Infos[2];
  
    $edition=$session->get('edition');
    $edition= $edition=$this->getDoctrine()->getManager()->merge($edition);
    $datelimcia = $edition->getDatelimcia();
    $datelimnat=$edition->getDatelimnat();
    $dateouverturesite=$edition->getDateouverturesite();
    $dateconnect= new \datetime('now');
       
    $equipe_choisie= $repositoryEquipesadmin->find(['id'=>$id_equipe]);
    $centre=$equipe_choisie->getCentre();
    
    
    $qb1 =$repositoryFichiersequipes->createQueryBuilder('t')//Les fichiers sans les autorisations photos 
                             ->LeftJoin('t.equipe', 'e')
                             ->Where('e.id=:id_equipe')
                              ->andWhere('e.edition =:edition')
                             ->setParameter('edition', $edition)
                             ->setParameter('id_equipe', $id_equipe);
    
     if ($concours=='academique'){
                      $qb1->andWhere('t.national =:national') 
                             ->andWhere('t.typefichier in (0,1,2,4,5)')
                             ->setParameter('national', FALSE) ;
                     
                     
                 }
    if ($concours =='national' ){
                             $qb1->andWhere('t.national =:national') 
                             ->andWhere('t.typefichier in (0,1,2,3)')
                             ->setParameter('national', TRUE)
                             ->orWhere('t.typefichier = 4 and  e.id=:id_equipe');;
                }
   
    $qb2 =$repositoryFichiersequipes->createQueryBuilder('t')    //pour le prof, le comité : tout  fichier cia ou cn
                             ->LeftJoin('t.equipe', 'e')
                             ->Where('e.id=:id_equipe')
                             ->setParameter('id_equipe', $id_equipe)
                            ->andWhere('e.edition =:edition')
                             ->setParameter('edition', $edition);
                            ;
                 if ($concours=='academique'){
                      $qb2->andWhere('t.typefichier in (0,1,2,4,5)');

                     $national=false;
                     
                 }
                 if ($concours =='national' ){
                             $qb2->andWhere('t.typefichier in (0,1,2,3)')
                                  ->orWhere('t.typefichier = 4  and e.id=:id_equipe');
                             $national=true;
                }
                
                
    $qb4 =$repositoryFichiersequipes->createQueryBuilder('t')  // /pour le jury cn resumé mémoire annexes diaporama
                             ->Where('t.equipe =:equipe')
                             ->setParameter('equipe', $equipe_choisie)
                             ->andWhere('t.typefichier in (0,1,2,3)');
                             //->andWhere('t.national =:national')
                             //->setParameter('national', TRUE) ;
    
    $listeEleves=$repositoryElevesinter->findByEquipe(['equipe'=>$equipe_choisie]);
    $liste_prof[1]= $repositoryUser->find(['id'=>$equipe_choisie->getIdProf1()]) ;
   if (null!=$equipe_choisie->getIdProf2()){
    $liste_prof[2]=$repositoryUser->find(['id'=>$equipe_choisie->getIdProf2()]) ;
      }
      
     
                            
    $roles=$this->getUser()->getRoles();
        $role=$roles[0];
      if ($role=='ROLE_COMITE'){
         $liste_fichiers=$qb2->getQuery()->getResult();    
         $autorisations=$qb1
                            ->andWhere('t.typefichier =:type3')
                           ->setParameter('type3',6)
                           ->getQuery()->getResult();       
       
      }            
      if($role=='ROLE_PROF'){  // Liste de tous les fichiers 
         
          $liste_fichiers=$qb1->getQuery()->getResult();    
          $autorisations=$qb1->andWhere('t.national =:national')
                            ->setParameter('national',$national)
                            ->andWhere('t.typefichier = 6')
                            ->getQuery()->getResult(); 
          
      }
      if( ($role=='ROLE_ORGACIA')  or ($role=='ROLE_SUPER_ADMIN')) {               
        $liste_fichiers=$qb1->getQuery()->getResult();    
        $autorisations=$qb1
                            ->andWhere('t.typefichier = 6')
                            ->getQuery()->getResult();
                }
       if ($role=='ROLE_JURYCIA'){         
           $qb1->andWhere('t.typefichier in (0,1,2,5)');

           $liste_fichiers=$qb1->getQuery()->getResult();
           
          
        $autorisations=[];
      } 
    if($role=='ROLE_JURY'){
         $liste_fichiers=$qb4->getQuery()->getResult();    
         
        }
      
    $infoequipe=$equipe_choisie->getInfoequipe();
    if ($equipe_choisie->getSelectionnee()==true ){
        $infoequipe=$equipe_choisie->getInfoequipenat();//pour les comités et jury,inutile pour les prof , ;
     }
     if($centre){
    $centre=$equipe_choisie->getCentre()->getCentre();
     }
    $user = $this->getUser();
    

     $qb = $repositoryVideosequipes->createQueryBuilder('v')
                             ->LeftJoin('v.equipe', 'e')
                             ->Where('e.id=:id_equipe')
                             ->setParameter('id_equipe', $id_equipe)
                            ->andWhere('e.edition =:edition')
                             ->setParameter('edition', $edition);
     $listevideos= $qb->getQuery()->getResult();
        if ($request->isMethod('POST') ) 
            {
            if ($request->request->has('FormAll')) {         
                $zipFile = new \ZipArchive();
                $FileName= $edition->getEd().'-Fichiers-eq-'.$equipe_choisie->getNumero().'-'.date('now');
                if ($zipFile->open($FileName, ZipArchive::CREATE) === TRUE){
                   $fichiers= $repositoryFichiersequipes->findByEquipe(['equipe'=>$equipe_choisie]);
                    
                   foreach($liste_fichiers as $fichier){
                        if($fichier){
                            if ($fichier->getTypefichier()==1){
                                
                                $fichierName=$this->getParameter('app.path.fichiers').'/'.$this->getParameter('type_fichier')[0].'/'.$fichier->getFichier();
                            }
                            else{
                         $fichierName=$this->getParameter('app.path.fichiers').'/'.$this->getParameter('type_fichier')[$fichier->getTypefichier()].'/'.$fichier->getFichier();
                            }
                        
                            $zipFile->addFromString(basename($fichierName),  file_get_contents($fichierName));//voir https://stackoverflow.com/questions/20268025/symfony2-create-and-download-zip-file
                          }
                        }
                    $zipFile->close();
                    $response = new Response(file_get_contents($FileName));//voir https://stackoverflow.com/questions/20268025/symfony2-create-and-download-zip-file
                    $disposition = HeaderUtils::makeDisposition(
                                            HeaderUtils::DISPOSITION_ATTACHMENT,
                                            $FileName
                                                  );
                    $response->headers->set('Content-Type', 'application/zip'); 
                    $response->headers->set('Content-Disposition', $disposition);
                    @unlink($FileName);
                    return $response; 
                    }
                }
            }
            
        if($liste_fichiers){      
            $fichier=new Fichiersequipes();
            $formBuilder=$this->get('form.factory')->createNamedBuilder('FormAll', ListefichiersType::class,$fichier); //Ajoute le bouton  tout télécharger
            $formBuilder->add('save',      SubmitType::class );
            $form=$formBuilder->getForm();
            $Form=$form->createView();
            
        }
         if(!isset($Form)) {
       
       $Form=$this->createForm(ListefichiersType::class)->createView(); 
        
         }
          if(!isset($listevideos)) {
             $listevideos=[];
         }
          if(!isset($autorisations)) {
             $autorisations=[];
         }
           if(!isset($liste_fichiers)) {
             $liste_fichiers=[];
         }
       

            
             $content = $this
                          ->renderView('adminfichiers\espace_prof.html.twig', array('form'=>$Form, 'listevideos'=>$listevideos,'liste_autorisations'=>$autorisations,
                                                        'equipe'=>$equipe_choisie, 'centre' =>$equipe_choisie->getCentre(),'concours'=>$concours, 'edition'=>$edition, 'choix'=>$choix, 'role'=>$role,
                                                         'liste_prof'=>$liste_prof, 'listeEleves'=>$listeEleves,'liste_fichiers'=>$liste_fichiers)
                                  ); 
            return new Response($content); 
            
            
}
 
       /**
         * @IsGranted("IS_AUTHENTICATED_ANONYMOUSLY")
         * 
         * @Route("/fichiers/choixedition,{num_type_fichier}", name="fichiers_choixedition")
         * 
         */    
        public function choixedition(Request $request, $num_type_fichier)
        {
            $repositoryEdition= $this->getDoctrine()
		->getManager()
		->getRepository('App:Edition');
            $qb=$repositoryEdition->createQueryBuilder('e')
                                      ->orderBy('e.ed', 'DESC');

            
            $Editions = $qb->getQuery()->getResult();
             return $this->render('adminfichiers/choix_edition.html.twig', [
                'editions' => $Editions, 'num_type_fichier'=>$num_type_fichier]);
        }
        /**
         *@IsGranted("IS_AUTHENTICATED_ANONYMOUSLY")
         * 
         * @Route("/fichiers/voirfichiers,{editionId_concours}", name="fichiers_voirfichiers")
         * 
         */    
        public function voirfichiers(Request $request, $editionId_concours)
        {
            $session=$this->requestStack->getSession();
            $editionconcours=explode('-',$editionId_concours);
        
            $IdEdition = $editionconcours[0];
            $concours = $editionconcours[1];
            $num_type_fichier=$editionconcours[2];
            $repositoryEdition = $this->getDoctrine()
		->getManager()
		->getRepository('App:Edition');
             $repositoryFichiersequipes = $this->getDoctrine()
		->getManager()
		->getRepository('App:Fichiersequipes');    
              $repositoryEquipesadmin = $this->getDoctrine()
		->getManager()
		->getRepository('App:Equipesadmin');    
              
            $edition = $repositoryEdition->find(['id'=>$IdEdition]);
            $edition_en_cours=$session->get('edition');
            $date=new \datetime('now');
            
          
            
            if ($concours=='cia'){
                 if ($edition_en_cours==$edition){
                    
                if ($edition_en_cours->getConcourscia()>$date){
                   $request->getSession()
                    ->getFlashBag()
                    ->add('info','Les fichiers de l\'édition '.$edition_en_cours->getEd().' ne sont pas encore publiés, patience ...') ;  
       return $this->redirectToRoute('fichiers_choixedition',array('num_type_fichier'=>$num_type_fichier)); 
                    
                    
                }
            } 
                
                
               $qb1= $repositoryFichiersequipes->createQueryBuilder('m')
                                      ->leftJoin('m.equipe', 'e')
                                      ->where('e.selectionnee=:selectionnee')
                                      ->orderBy('e.lyceeAcademie', 'ASC')
                                     ->setParameter('selectionnee',FALSE)
                                      ->andWhere('m.edition=:edition')
                                      ->setParameter('edition', $edition)
                                      ->andWhere('m.typefichier <:type')
                                       ->setParameter('type',3);
                                
               $fichierstab=$qb1->getQuery()->getResult();
                $qb2= $repositoryEquipesadmin->createQueryBuilder('e')
                                      ->where('e.selectionnee=:selectionnee')
                                      ->setParameter('selectionnee',FALSE)
                                      ->orderBy('e.lyceeAcademie', 'ASC');
                                     
                  $listeequipe=$qb2->getQuery()->getResult();
          }
            if ($concours=='cn'){
                 if ($edition_en_cours==$edition){
                 if ($edition_en_cours->getConcourscn()>$date){
                   $request->getSession()
                    ->getFlashBag()
                    ->add('info','Les fichiers de l\'édition '.$edition_en_cours->getEd().' ne sont pas encore publiés, patience ...') ;  
       return $this->redirectToRoute('fichiers_choixedition',array('num_type_fichier'=>$num_type_fichier)); 
                    
                    
                 }}
                $qb1= $repositoryFichiersequipes->createQueryBuilder('m')
                                      ->leftJoin('m.equipe', 'e')
                                      ->orderBy('e.lettre', 'ASC')
                                      ->andWhere('m.edition=:edition')
                                      ->setParameter('edition', $edition);
                 if($num_type_fichier==0){       
                                     $qb1->andWhere('m.typefichier <:type')
                                     ->setParameter('type',3);
                 }
                 
                  if($num_type_fichier==3){       
                                     $qb1->andWhere('m.typefichier =:type')
                                     ->setParameter('type',$num_type_fichier) ->andWhere('m.edition=:edition')
                                      ->setParameter('edition', $edition);
                 }
               $fichierstab=$qb1->getQuery()->getResult();
                          
               
               $qb2= $repositoryEquipesadmin->createQueryBuilder('e')
                                      ->where('e.selectionnee=:selectionnee')
                                      ->setParameter('selectionnee',TRUE)
                                      ->orderBy('e.lettre', 'ASC');
                                      
                  $listeequipe=$qb2->getQuery()->getResult();
            }
             
             if($listeequipe){
                
             $i=0;
            foreach($listeequipe as $equipe){
                if ($fichierstab){
               $j=0;
                foreach($fichierstab as $fichier){
                    
                
                    if ($fichier->getEquipe()==$equipe){
                        $fichiersEquipe[$i][$j]=$fichier;   
                            $j++;
                       }
                } 
            }
             $i++;
              }
              
            if (isset($fichiersEquipe)){
                if($num_type_fichier<3){
              $content = $this
                          ->renderView('adminfichiers\affiche_memoires.html.twig',
                                  array('fichiersequipes'=>$fichiersEquipe,
                                           'edition'=>$edition, 
                                            'concours'=>$concours
                                            )); 
                }
                 if($num_type_fichier==3){
                     
              $content = $this
                          ->renderView('adminfichiers\affiche_presentations.html.twig',
                                  array('fichiersequipes'=>$fichiersEquipe,
                                           'edition'=>$edition, 
                                            'concours'=>$concours
                                            )); 
                }
            return new Response($content); 
            }
              else
        {$request->getSession()
                    ->getFlashBag()
                    ->add('info','Pas de fichier déposé à ce jour pour cette édition  ') ;  
       return $this->redirectToRoute('fichiers_choixedition',array('num_type_fichier'=>$num_type_fichier));  
        }
        }
}
         /**
         *@IsGranted("ROLE_COMITE")
         * 
         * @Route("/fichiers/charge_autorisations", name="fichiers_charge_autorisations")
         * 
         */    
public function charge_autorisation(Request $request){
    $repositoryFichiersequipes = $this->getDoctrine()
		->getManager()
		->getRepository('App:Fichiersequipes'); 
   $query=$request->query;;
  
   for($i=0;$i<8;$i++){
       
       try{
           if($query->get('check-eleve-'.$i)=="on"){
           $autorisationelevesid[$i]=explode('-',$query->get('eleve-'.$i))[1];}
              if($query->get('check-prof-'.$i)=="on"){
       $autorisationprofsid[$i]=explode('-',$query->get('prof-'.$i))[1];
              }
       }
       catch(\Exception $e){
       
       }
    
   }
 
   $zipFile = new \ZipArchive();
                $FileName='Autorisations'.date('now');
                if ($zipFile->open($FileName, ZipArchive::CREATE) === TRUE){
                    
                    if (isset($autorisationprofsid)){
                   foreach( $autorisationprofsid as $id) {
                   $fichierprof= $repositoryFichiersequipes->findOneById(['id'=>$id]);
                   $fichierName=$this->getParameter('app.path.fichiers').'/autorisations/'.$fichierprof->getFichier();
                    $zipFile->addFromString(basename($fichierName),  file_get_contents($fichierName));
                    }}
                     if (isset($autorisationelevesid)){
                    foreach( $autorisationelevesid as $id) {
                   $fichiereleve= $repositoryFichiersequipes->findOneById(['id'=>$id]);
                   $fichierName=$this->getParameter('app.path.fichiers').'/autorisations/'.$fichiereleve->getFichier();
                    $zipFile->addFromString(basename($fichierName),  file_get_contents($fichierName));
                   }
                     }
                    $zipFile->close();
                    $response = new Response(file_get_contents($FileName));//voir https://stackoverflow.com/questions/20268025/symfony2-create-and-download-zip-file
                    $disposition = HeaderUtils::makeDisposition(
                                            HeaderUtils::DISPOSITION_ATTACHMENT,
                                            $FileName
                                                  );
                    $response->headers->set('Content-Type', 'application/zip'); 
                    $response->headers->set('Content-Disposition', $disposition);
                    @unlink($FileName);
                    return $response; 
                    }
  
}



   /**
         *@IsGranted("ROLE_SUPER_ADMIN")
         * 
         * @Route("/fichiers/transpose_donnees,", name="fichiers_transpose_donnees")
         * 
         */    
public function transpose_donnees(Request $request){
    
    $repositoryFichiersequipes = $this->getDoctrine()
		->getManager()
		->getRepository('App:Fichiersequipes'); 
    $repositoryMemoires = $this->getDoctrine()
		->getManager()
		->getRepository('App:Memoires'); 
     $repositoryMemoiresinter = $this->getDoctrine()
		->getManager()
		->getRepository('App:Memoiresinter');  
      $repositoryResumes = $this->getDoctrine()
		->getManager()
		->getRepository('App:Resumes');  
       $repositoryFichessecur = $this->getDoctrine()
		->getManager()
		->getRepository('App:Fichessecur');  
        $repositoryPresentations = $this->getDoctrine()
		->getManager()
		->getRepository('App:Presentation');  
    $repositoryEquipesadmin = $this->getDoctrine()
		->getManager()
		->getRepository('App:Equipesadmin');
     $repositoryEdition = $this->getDoctrine()
		->getManager()
		->getRepository('App:Edition');
    
    $liste_memoires=$repositoryMemoires->findAll();
     $liste_memoiresinter=$repositoryMemoiresinter->findAll();
    $liste_resumes=$repositoryResumes->findAll();
    $liste_Fichessecur=$repositoryFichessecur->findAll();
    $liste_Presentations=$repositoryPresentations->findAll();
    $File_system=new FileSystem();
    $em=$this->getDoctrine()->getManager();
   if (isset($liste_memoires)){
       foreach($liste_memoires as $memoire){
           $Fichier=new Fichiersequipes();
           $Fichier->setEdition($memoire->getEdition());
           $Fichier->setNational(1);
           $Fichier->setEquipe($memoire->getEquipe());
           if ($memoire->getAnnexe()==false){
                $Fichier->setTypefichier(0);
           }else
           {$Fichier->setTypefichier(1);               
           }
         $File_system->copy($this->getParameter('app.path.memoires_nat').'/'.$memoire->getMemoire(),$this->getParameter('app.path.tempdirectory').'/'.$memoire->getMemoire()); 
           $fichier =new UploadedFile($this->getParameter('app.path.tempdirectory').'/'.$memoire->getMemoire(),$memoire->getMemoire(),null,null,true);
         
           $Fichier->setFichierFile($fichier);
           
           $em->persist($Fichier);
           $em->flush();
                
          }       
        }   
    
     if (isset($liste_memoiresinter)){
       foreach($liste_memoiresinter as $memoire){
           $Fichier=new Fichiersequipes();
           $Fichier->setEdition($memoire->getEdition());
           $Fichier->setNational(0);
           $Fichier->setEquipe($memoire->getEquipe());
           if ($memoire->getAnnexe()==false){
                $Fichier->setTypefichier(0);
           }else
           {$Fichier->setTypefichier(1);               
           }
           $File_system->copy($this->getParameter('app.path.memoires_inter').'/'.$memoire->getMemoire(),$this->getParameter('app.path.tempdirectory').'/'.$memoire->getMemoire());
           $fichier =new UploadedFile($this->getParameter('app.path.tempdirectory').'/'.$memoire->getMemoire(),$memoire->getMemoire(),null,null,true);
         
           $Fichier->setFichierFile($fichier);
           
           $em->persist($Fichier);
           $em->flush();
                
          }       
        }
    if (isset($liste_resumes)){
       foreach($liste_resumes as $resume){
           $Fichier=new Fichiersequipes();
           $Fichier->setEdition($resume->getEdition());
           $Fichier->setNational(0);
           $Fichier->setEquipe($resume->getEquipe());
           $Fichier->setTypefichier(2);          
           $File_system->copy($this->getParameter('app.path.resumes').'/'.$resume->getResume(),$this->getParameter('app.path.tempdirectory').'/'.$resume->getResume());
           $fichier =new UploadedFile($this->getParameter('app.path.tempdirectory').'/'.$resume->getResume(),$resume->getResume(),null,null,true);
         
           $Fichier->setFichierFile($fichier);
           
           $em->persist($Fichier);
           $em->flush();
                
          }       
        }    
         if (isset($liste_Fichessecur)){
       foreach($liste_Fichessecur as $fiche){
           if ($fiche->getFiche()){
           $Fichier=new Fichiersequipes();
           
         
               $edition=$repositoryEdition->find(['id'=>1]);
           $fiche->setEdition($edition);
           $Fichier->setEdition($edition);
           $Fichier->setNational(0);
           $Fichier->setEquipe($fiche->getEquipe());
           $Fichier->setTypefichier(4);          
           $File_system->copy($this->getParameter('app.path.fichessecur').'/'.$fiche->getFiche(),$this->getParameter('app.path.tempdirectory').'/'.$fiche->getFiche());
           $fichier =new UploadedFile($this->getParameter('app.path.tempdirectory').'/'.$fiche->getFiche(),$fiche->getFiche(),null,null,true);
         
           $Fichier->setFichierFile($fichier);
           
           $em->persist($Fichier);
           $em->flush();
           }  
          }       
        }  
         if (isset($liste_Presentations)){
             
       foreach($liste_Presentations as $presentation){
           $Fichier=new Fichiersequipes();
           $Fichier->setEdition($presentation->getEdition());
           $Fichier->setNational(1);
           $Fichier->setEquipe($presentation->getEquipe());
           $Fichier->setTypefichier(3);          
           $File_system->copy($this->getParameter('app.path.presentations').'/'.$presentation->getPresentation(),$this->getParameter('app.path.tempdirectory').'/'.$presentation->getPresentation());
           $fichier =new UploadedFile($this->getParameter('app.path.tempdirectory').'/'.$presentation->getPresentation(),$presentation->getPresentation(),null,null,true);
         
           $Fichier->setFichierFile($fichier);
           
           $em->persist($Fichier);
           $em->flush();
                
          }       
          return $this->redirectToRoute('core_home');
        }  
        
        
        
    
}

}
        
                           
                   
  
