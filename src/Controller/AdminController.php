<?php
namespace App\Controller;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Bundle\MakerBundle\Str;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\EasyAdminFiltersFormType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\EntityRepository;
use ZipArchive;
use App\Entity\Equipesadmin;

class AdminController extends EasyAdminController
{
     private $passwordEncoder;
     public $password;
   public function __construct(UserPasswordEncoderInterface $passwordEncoder,SessionInterface $session)
     {
         $this->passwordEncoder = $passwordEncoder;
         $this->session=$session;
         
     }
    /**
     * @Route("/", name="easyadmin")
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     *
     * @Security("is_granted('ROLE_COMITE')")
     */
    public function indexAction(Request $request)
    {
        //$this->initialize($request);
        // if the URL doesn't include the entity name, this is the index page  // if the URL doesn't include the entity name, this is the index page
        //dd($request);
       
        if (null === $request->query->get('entity')) {
            // define this route in any of your own controllers
             $content = $this->renderView('Admin/content.html.twig',array());
            
            $this->session->set('edition_titre',$this->session->get('edition')->getEd());
            
             return new Response($content);
             
        }
        
        return parent::indexAction($request);
   }


    
    
   
     public function LireAction()
     {    $fichier='';
          $class = $this->entity['class'];
         $repository = $this->getDoctrine()->getRepository($class);
         $id = $this->request->query->get('id');
         $entity = $repository->find($id);
        
         if ($class==Fichiersequipes::class) {
             
             if(($entity->getTypefichier() ==0) or ($entity->getTypefichier() ==1)  ){
                 
                 $fichier= $this->getParameter('app.path.fichiers').'/'.$this->getParameter('type_fichier')[0].'/'.$entity->getFichier();
             }
             else{
                 $fichier= $this->getParameter('app.path.fichiers').'/'.$this->getParameter('type_fichier')[$entity->getTypefichier()].'/'.$entity->getFichier();
             }
               
                 $file=new File($fichier);
                    $response = new BinaryFileResponse($fichier);
         
                    $disposition = HeaderUtils::makeDisposition(
                      HeaderUtils::DISPOSITION_ATTACHMENT,

                     $entity->getFichier()
                            );
                    $response->headers->set('Content-Type', $file->guessExtension()); 
                    $response->headers->set('Content-Disposition', $disposition);
        
                  return $response; 
               
                  }
     
         if ($class==Photosinter::class)
         {
              $fichier=$this->getParameter('repertoire_photosinter').'/'.$entity->getPhoto();
              $application= 'image/jpeg';
              $name=$entity->getPhoto();
         }
          
         if ($class==Photoscn::class)
         {
              $fichier=$this->getParameter('app.path.photosnat').'/'.$entity->getPhoto();
              
              $application= 'image/jpeg';
              $name=$entity->getPhoto();
         }
         
         $response = new BinaryFileResponse($fichier);
         
         $disposition = HeaderUtils::makeDisposition(
           HeaderUtils::DISPOSITION_ATTACHMENT,
                 
           $name
                 );
         $response->headers->set('Content-Type', $application); 
         $response->headers->set('Content-Disposition', $disposition);
         
        
         //$content = $this->render('secretariat\lire_memoire.html.twig', array('repertoirememoire' => $this->getParameter('repertoire_memoire_national'),'memoire'=>$fichier));
         return $response; 
         
    }
    public function EnregistrerAction() {
        $fichier='';
          $class = $this->entity['class'];
         $repository = $this->getDoctrine()->getRepository($class);
         $id = $this->request->query->get('id');
         $entity = $repository->find($id);
        
        if ($class==Photoscn::class)
         {
              $fichier=$this->getParameter('app.path.photosnat').'/'.$entity->getPhoto();
             
              $application= 'image/jpeg';
              $name=$entity->getPhoto();
         }
         
         if ($class==Photosinter::class)
         {
              $fichier=$this->getParameter('repertoire_photosinter').'/'.$entity->getPhoto();
              
              $application= 'image/jpeg';
              $name=$entity->getPhoto();
         }
         
         $response = new BinaryFileResponse($fichier);
         
         $disposition = HeaderUtils::makeDisposition(
           HeaderUtils::DISPOSITION_ATTACHMENT,
                 
           $name
                 );
         $response->headers->set('Content-Type', $application); 
         $response->headers->set('Content-Disposition', $disposition);
         
        
         //$content = $this->render('secretariat\lire_memoire.html.twig', array('repertoirememoire' => $this->getParameter('repertoire_memoire_national'),'memoire'=>$fichier));
         return $response; 
        
        
    }
   public function EditAction(){
      $idequipe=$this->request->query->get('id');
       $class = $this->entity['class'];
       if ($class=='equipe'){
        $repository = $this->getDoctrine()->getRepository($class);
        $cadeau=$repository->find(['id'=>$idequipe])->getCadeau();
        $visite=$repository->find(['id'=>$idequipe])->getVisite();
      $this->session->set('idcadeau',$cadeau);
       $this->session->set('idvisite',$visite);
       }
     return parent::editAction();  
   }
   
  
        
    
    public function persistUserEntity($entity)
    {
        $this->updateUserPassword($entity);
        parent::persistEntity($entity);
    }

    public function updateUserEntity($entity)
    {
        $this->updateUserPassword($entity);
        parent::updateEntity($entity);
    }

    private function updateUserPassword($entity)
    {  
         $password=$entity->getPassword();
        if (method_exists($entity, 'setPassword') ) {
            $entity->setPassword($this->passwordEncoder->encodePassword($entity, $password));
        }
    }
    
    public function telechargerBatchAction(array $ids)
    {
        $class = $this->entity['class'];
       
        $repository = $this->getDoctrine()->getRepository($class);
        $zipFile = new \ZipArchive();
         if ($class==Fichessecur::class) { 
         $FileName= 'Fiches_securite'.date('now');    
         }
        if ($class==Memoiresinterr::class) { 
         $FileName= 'memoires'.date('now');
            
        }
        if ($class==Resumes::class) { 
            $FileName= 'resumes'.date('now');
        }
        if ($class==Photosinter::class) { 
            $FileName= 'photos'.date('now');
        }
         if ($class==Fichiersequipesautorisarions::class) { 
            $FileName= 'autorisationsphotos'.date('now');
        }
        
        if ($zipFile->open($FileName, ZipArchive::CREATE) === TRUE)
        {
            foreach ($ids as $id) 
                {


                    $entity = $repository->find($id);
                    if ($class==Fichessecur::class) { 
                       
                    $fichier= $this->getParameter('repertoire_fiches_securite').'/'.$entity->getFiche();}
                    if ($class==Memoiresinter::class) {
                    $fichier=$this->getParameter('repertoire_memoire_interacademiques').'/'.$entity->getMemoire();}
                     if ($class==Resumes::class) {
                    $fichier=$this->getParameter('repertoire_resumes').'/'.$entity->getResume();}
                    if ($class==Photosinter::class) {
                    $fichier=$this->getParameter('repertoire_photosinter').'/'.$entity->getPhoto();}
                     if ($class==Fichiersequipesautorisations::class) {
                    $fichier=$this->getParameter('app.path.fichiers ').'/autorisations/'.$entity->getFichier();}
                    //$nom_memoire=$entity->getMemoire();
                    //$filenameFallback = iconv('UTF-8','ASCII//TRANSLIT',$nom_memoire);
                    $zipFile->addFromString(basename($fichier),  file_get_contents($fichier));//voir https://stackoverflow.com/questions/20268025/symfony2-create-and-download-zip-file

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
                //$content = $this->render('secretariat\lire_memoire.html.twig', array('repertoirememoire' => $this->getParameter('repertoire_memoire_national'),'memoire'=>$fichier));
                return $response; 
    } 
            
   }
                
     

    
    
     public function deleteAction()
    {  $class = $this->entity['class'];    
         
         $entityManager = $this->getDoctrine()->getManager();
        if ($class== 'App\Entity\Equipesadmin') //Pour effacer les élèves d'une équipe qui est supprimée
        {
         
           $repositoryElevesinter = $this->getDoctrine()->getRepository('App:Elevesinter');
           
           $id = $this->request->query->get('id');
          $equipe = $repositoryEquipesadmin->find($id);
          $equipe->setCentre(null);
           $eleves= $repositoryElevesinter->findBy(['equipe'=>$equipe]);
           If ($eleves){
               foreach($eleves as $eleve)
               {   $eleve->setEquipe(null);
                   $entityManager->remove($eleve);
                   $entityManager->flush();
                   
               }
           }
        }
        if ($class== 'App\Entity\Centrescia')
        {
            $repositoryCentrescia = $this->getDoctrine()->getRepository('App:Centrescia');
            $id = $this->request->query->get('id');
            $centre=$repositoryCentrescia->find($id);   
             $repositoryEquipesadmin = $this->getDoctrine()->getRepository('App:Equipesadmin');
             $equipes=$repositoryEquipesadmin->findByCentre(['centre'=>$centre]);
          foreach($equipes as $equipe){
              $equipe->setCentre(null);
              
              $entityManager->persist($equipe);
               $entityManager->flush();
          }
          $repositoryUser = $this->getDoctrine()->getRepository('App:User');
          
          $users=$repositoryUser->findByCentrecia(['centrecia'=>$centre]);
          foreach($users as $user){
              $user->setCentre(null);
              $entityManager->persist($user);
              $entityManager->flush();
          }
        }
        if ($class=='App\Entity\Photoscn'){
            $repositoryPhotoscn=$this->getDoctrine()->getRepository('App:Photoscn');
            $id= $this->request->query->get('id');
            $image= $repositoryPhotoscn->find(['id'=>$id]);
            $thumb=$repositoryPhotoscn->find(['id'=>$id])->getThumb();
            $image->setThumb(null);
            $entityManager->remove($thumb);
            $entityManager->flush();
            
            
        }
         if ($class=='App\Entity\Photosinter'){
            $repositoryPhotosinter=$this->getDoctrine()->getRepository('App:Photosinter');
            $id= $this->request->query->get('id');
            $image= $repositoryPhotosinter->find(['id'=>$id]);
            $thumb=$repositoryPhotosinter->find(['id'=>$id])->getThumb();
            $image->setThumb(null);
            $entityManager->remove($thumb);
            $entityManager->flush();
            
            
        }
        
         if ($class=='App\Entity\Fichiersequipes'){
            $repositoryFichiers=$this->getDoctrine()->getRepository(Fichiersequipes::class);
            $id= $this->request->query->get('id');
            dd($this->entity);
            $fichier= $repositoryFichiers->find(['id'=>$id]);
            $repositoryEleves = $this->getDoctrine()->getRepository(Elevesinter::class);
            $repositoryUser = $this->getDoctrine()->getRepository(User::class);
            if($fichier->getTypefichier()==6)
            $eleve=$repositoryEleves->findOneByAutorisationphotos(['autorisationphotos'=>$fichier]);
                $prof= $repositoryUser->findOneByAutorisationphotos(['autorisationphotos'=>$this->entity]);
                dump($eleve);
                dd($prof);
                if (($eleve) and (!$prof)){
                $eleve->setAutorisationphotos(null);
                $entityManager->persist($eleve);
                
               $this->entityr->setEquipe(null);   
                 }
                   if ((!$eleve) and ($prof)){
                $prof->setAutorisationphotos(null);
                $entityManager->persist($prof);
                              
                 }
                $this->entity->setEdition(null);
                $entityManager->remove($this->entity);
                $entityManager->flush();
            
            
        }
        
        
        
        
        
        
        
        
      return parent::deleteAction();
    }
    
    public function deleteBatchAction(array $ids):void
    {         $class = $this->entity['class']; 
              $entityManager = $this->getDoctrine()->getManager();
    
              if ($class== 'App\Entity\Equipesadmin'){
             $repositoryEquipesadmin = $this->getDoctrine()->getRepository('App:Equipesadmin');
          $repositoryFichessecur = $this->getDoctrine()->getRepository('App:Fichessecur');
           $repositoryMemoiresinter = $this->getDoctrine()->getRepository('App:Memoiresinter');
           $repositoryResumes = $this->getDoctrine()->getRepository('App:Resumes');     
           $repositoryElevesinter = $this->getDoctrine()->getRepository('App:Elevesinter'); 
           $repositoryMemoires = $this->getDoctrine()->getRepository('App:Memoires');
            foreach($ids as $id){
           $equipe = $repositoryEquipesadmin->find($id);
           $equipe->setCentre(null);
           
           ($equipe);
           $eleves= $repositoryElevesinter->findBy(['equipe'=>$equipe]);
           If ($eleves){
               foreach($eleves as $eleve)
               {   $eleve->setEquipe(null);
                   $entityManager->remove($eleve);
                   $entityManager->flush();
                   
               }
           }
           $fichessecur= $repositoryFichessecur->findOneBy(['equipe'=>$equipe]);
           if($fichessecur){
               $fichessecur->setEquipe(null);
              $entityManager->remove($fichessecur);
               $entityManager->flush();
           }
             $memoires= $repositoryMemoiresinter->findByEquipe(['equipe'=>$equipe]);
             
             
           if($memoires){
               foreach($memoires as $memoire)
              
              $memoire->setEquipe(null);
              $entityManager->remove($memoire);
               $entityManager->flush();
          }        
           $resume= $repositoryResumes->findOneByEquipe(['equipe'=>$equipe]);
           if($resume){
               $resume->setEquipe(null);
              $entityManager->remove($resume);
               $entityManager->flush();
        
          }
            $memoiresnat= $repositoryMemoires->findByEquipe(['equipe'=>$equipe]);
           if($memoiresnat){
               foreach($memoiresnat as $memoire){
               $memoire->setEquipe(null);
              $entityManager->remove($memoire);
               $entityManager->flush();
               
               }
        
          }
          
          
          $entityManager->remove($equipe);
               $entityManager->flush();
        }
         
            }
            if ($class== 'App\Entity\Elevesinter') //Pour effacer les élèves
        {$repositoryElevesinter = $this->getDoctrine()->getRepository('App:Elevesinter');
       
             
           foreach($ids as $id){
               $eleve= $repositoryElevesinter->find(['id'=>$id]);
                  $eleve->setEquipe(null);
                   $entityManager->remove($eleve);
                   $entityManager->flush();
                   
               }
           }
           
            
            
              
        }
        
    
   // public function updateMemoiresEntity($entity)
     //{ 
     // $equipe = $entity->getEquipe();
       //$equipe->setMemoire($entity);  
      // parent::persistEntity($entity);
       
     
    //}
    
    
     
     
}
