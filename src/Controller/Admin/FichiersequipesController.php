<?php
namespace App\Controller\Admin;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType ; 
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\FormInterface;
use App\Entity\Equipesadmin;
use App\Entity\Fichiersequipes;
use App\Entity\Elevesinter;
use App\Entity\Edition;
use App\Entity\Centrescia;
use App\Entity\User;
use App\Form\Filter\EquipesadminFilterType;
use App\Form\Filter\FichiersequipesFilterType;
use App\Form\Filter\AutorisationsFilterType;

use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use ZipArchive;
use EasyCorp\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use Symfony\Component\String\UnicodeString;

class FichiersequipesController extends EasyAdminController
{   public function __construct(SessionInterface $session)
        {
            $this->session = $session;
            
        }
    
    
    
    
    protected function createFiltersForm(string $entityName): FormInterface
    {  
       
        $form = parent::createFiltersForm($entityName);
       
        $form->add('edition', FichiersequipesFilterType::class, [
            'class' => Edition::class,
            'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('u')
                                    ->addOrderBy('u.ed', 'DESC');
                                     },
           'choice_label' => 'getEd',
            'multiple'=>false,]);
        
            if(($entityName=='Fichiersequipesmemoiresinter') or ($entityName=='Fichiersequipesresumes')){                         
            $form->add('centre', FichiersequipesFilterType::class, [
                         'class' => Centrescia::class,
                         'query_builder' => function (EntityRepository $er) {
                                         return $er->createQueryBuilder('u')
                                                 ->addOrderBy('u.centre','ASC');
                                                      
                                                  },
                        'choice_label' => 'getCentre',
                         'multiple'=>false,]);
            
                                    
            $form->add('equipe', FichiersequipesFilterType::class, [
                         'class' => Equipesadmin::class,
                         'query_builder' => function (EntityRepository $er) {
                                         return $er->createQueryBuilder('u')
                                                 ->andWhere('u.selectionnee =:selectionnee')
                                                 ->setParameter('selectionnee','FALSE')
                                                 ->addOrderBy('u.centre','ASC')
                                                 ->addOrderBy('u.numero','ASC');     
                                                  },
                        'choice_label' => 'getInfoequipe',
                         'multiple'=>false,]);
            }
            
             if(($entityName=='Fichiersequipesmemoirescn') or ($entityName=='Fichiersequipesresumescn')){                         
                      
                                    
            $form->add('equipe', FichiersequipesFilterType::class, [
                         'class' => Equipesadmin::class,
                         'query_builder' => function (EntityRepository $er) {
                                         return $er->createQueryBuilder('u')
                                                 ->andWhere('u.selectionnee =:selectionnee')
                                                 ->setParameter('selectionnee',TRUE)
                                                 ->addOrderBy('u.edition','DESC')
                                                 ->addOrderBy('u.lettre','ASC');
                                                      
                                                  },
                        'choice_label' => 'getInfoequipenat',
                         'multiple'=>false,]);
                                                  
            }
           
            
            //$form->add('submit', SubmitType::class, [ 'label' => 'Appliquer',  ]);
          if($entityName=='Fichiersequipesautorisations'){                         
                      
                                    
            $form->add('eleve', AutorisationsFilterType::class, [
                         'class' => Elevesinter::class,
                         'query_builder' => function (EntityRepository $er) {
                                         return $er->createQueryBuilder('e')
                                                 ->LeftJoin('e.autorisationphotos','aut')
                                                // ->andWhere('aut.edition =:edition')
                                               //  ->setParameter('edition',$this->session->get('edition)'))
                                                 ->LeftJoin('e.equipe','eq')
                                                 ->addOrderBy('eq.numero','ASC')
                                                 ->addOrderBy('e.nom','ASC');
                                               
                                                      
                                                  },
                        'choice_label' => 'getNomPrenom',
                         'multiple'=>false,]);
              $form->add('equipe', AutorisationsFilterType::class, [
                         'class' => Equipesadmin::class,
                         'query_builder' => function (EntityRepository $er) {
                                         return $er->createQueryBuilder('u')
                                                 ->andWhere('u.edition =:edition')
                                                 ->setParameter('edition', $this->session->get('edition'))
                                                 ->addOrderBy('u.numero','ASC');
                                                      
                                                  },
                        'choice_label' => 'getInfoequipe',
                         'multiple'=>false,]);       
                                                
            $form->add('prof', AutorisationsFilterType::class, [
                         'class' => User::class,
                         'query_builder' => function (EntityRepository $er) {
                                         $qb=$er->createQueryBuilder('p');  
                                         return $er->createQueryBuilder('u')
                                          ->andWhere($qb->expr()->like('u.roles',':roles'))
                                           ->setParameter('roles','%i:0;s:9:"ROLE_PROF";i:2;s:9:"ROLE_USER";%')        
                                           ->addOrderBy('u.nom', 'ASC');           
                                                  },
                        'choice_label' => 'getNomPrenom',
                         'multiple'=>false,]);                                            
                                                  
                                                  
                                                  
          }
        
        return $form;
    
    }
    public function updateEntity($entity)//après un edit
    {  
        
           if ($this->session->get('concours')=='interacadémique')
           {
               $entity->setNational(0);
           }
           if ($this->session->get('concours')=='national')
           {
               $entity->setNational(1);
           }
         $class = $this->entity['class'];
       
        $em=$this->getDoctrine()->getManager();
         $FichierRepository = $this->getDoctrine()->getRepository($class);
         $fichier=$FichierRepository->find(['id'=>$entity->getId()]);
          $UserRepository=$this->getDoctrine()
		->getManager()
		->getRepository(User::class);
          $EleveRepository=$this->getDoctrine()
		->getManager()
		->getRepository(Elevesinter::class);
         $equipe=$entity->getEquipe();
         $citoyen=$EleveRepository->findOneByAutorisationphotos(['autorisationphotos'=>$entity]);
          if (!$citoyen){
              
           $citoyen   = $UserRepository->findOneByAutorisationphotos(['autorisationphotos'=>$entity]);
          }
           $entity->setNomautorisation($citoyen->getNom().'-'.$citoyen->getPrenom());
          
           parent::persistEntity($entity);
         
    }
   
    public  function createListQueryBuilder($entityClass, $sortDirection, $sortField = null, $dqlFilter = null){
          
       
        $edition= $this->session->get('edition');
         $this->session->set('edition_titre',$edition->getEd());
            $em = $this->getDoctrine()->getManagerForClass($this->entity['class']);
        /* @var DoctrineQueryBuilder */
        $queryBuilder = $em->createQueryBuilder('l')
            ->select('entity')
            ->from($this->entity['class'], 'entity')
           ->andWhere('entity.edition =:edition');
           
        
        if (!empty($dqlFilter)) {
           
              if ($dqlFilter=='entity.typefichier < 2  AND  entity.national = 1'){
              $queryBuilder->andWhere('entity.typefichier < 2')
                      ->join('entity.equipe','eq')
                      -> andWhere('entity.national = TRUE')
                      ->andWhere('eq.edition =:edition')
                     ->setParameter('edition', $edition)
                       ->addOrderBy('eq.lettre', 'ASC');;
              }
              if ($dqlFilter=='entity.typefichier < 2 AND  entity.national = false'){
              $queryBuilder->andWhere('entity.typefichier < 2')
                       ->join('entity.equipe','eq')
                      ->andWhere('entity.national = FALSE')
                      ->addOrderBy('eq.centre', 'ASC')
                      ->andWhere('eq.edition =:edition')
                      ->setParameter('edition', $edition)
                        ->addOrderBy('eq.numero', 'ASC');;
                       
              }
               if ($dqlFilter=='entity.typefichier = 4'){
              $queryBuilder->andWhere($dqlFilter)
                       ->join('entity.equipe','eq')
                       ->addOrderBy('eq.numero', 'ASC')
                      ->andWhere('eq.edition =:edition')
                     ->setParameter('edition', $edition)
                      ->addOrderBy('eq.lettre', 'ASC');;;;
              }
              
               if ($dqlFilter=='entity.typefichier = 2'){
              $queryBuilder->andWhere($dqlFilter)
                        ->andWhere('entity.national = FALSE')
                      ->join('entity.equipe','eq')
                       ->addOrderBy('eq.numero', 'ASC')
                      ->andWhere('eq.edition =:edition')
                      ->setParameter('edition', $edition)
                      ->addOrderBy('eq.lettre', 'ASC');;;;
              }
               if ($dqlFilter=='entity.typefichier = 2 AND entity.national = 1'){
              $queryBuilder
                        ->andWhere('entity.typefichier = 2') 
                       ->join('entity.equipe','eq')
                       ->andWhere('entity.national = TRUE')
                       ->addOrderBy('eq.numero', 'ASC')
                      ->andWhere('eq.edition =:edition')
                      ->setParameter('edition', $edition)
                      ->addOrderBy('eq.lettre', 'ASC');;;;
              }
              
              
              
               if ($dqlFilter=='entity.typefichier = 3 AND national = 1'){
                   
              $queryBuilder->andWhere('entity.typefichier = 3')
                       ->join('entity.equipe','eq')
                      -> andWhere('entity.national = TRUE')
                      ->andWhere('eq.edition =:edition')
                      ->setParameter('edition', $edition)
                       ->addOrderBy('eq.numero', 'ASC')
                      ->addOrderBy('eq.lettre', 'ASC');;;;
              }  
               if ($dqlFilter=='entity.typefichier = 5'){
              $queryBuilder->andWhere($dqlFilter)
                       ->join('entity.equipe','eq')
                       ->addOrderBy('eq.numero', 'ASC')
                      ->andWhere('eq.edition =:edition')
                      ->setParameter('edition', $edition)
                       ->addOrderBy('eq.lettre', 'ASC');;;;
              }
               if ($dqlFilter=='entity.typefichier = 6'){
              $queryBuilder->andWhere($dqlFilter)
                      ->setParameter('edition', $edition);
                   
              }
        } 
            return $queryBuilder;
         
      }
     public function LireAction()
     {    $fichier='';
          $class = $this->entity['class'];
         $repository = $this->getDoctrine()->getRepository($class);
         $id = $this->request->query->get('id');
         $entity = $repository->find($id);
        
         
             
             if(($entity->getTypefichier() ==0) or ($entity->getTypefichier() ==1)  ){
                 
                 $fichier= $this->getParameter('app.path.fichiers').'/'.$this->getParameter('type_fichier')[0].'/'.$entity->getFichier();
             }
             else{
                 $nom_fichier=$entity->getFichier();
                
                 $fichier= $this->getParameter('app.path.fichiers').'/'.$this->getParameter('type_fichier')[$entity->getTypefichier()].'/'.$nom_fichier;
                 
             }
               
                 $file=new File($fichier);
                 try{
                     
                     
                    
                    $response = new BinaryFileResponse($fichier);
                    
                 
                 
                    $disposition = HeaderUtils::makeDisposition(
                      HeaderUtils::DISPOSITION_ATTACHMENT,

                     $entity->getFichier()
                            );
                    $response->headers->set('Content-Type', $file->guessExtension()); 
                    $response->headers->set('Content-Disposition', $disposition);
                 
                  return $response; 
                 }
                    catch(\Exception $e){
                 
                 $this->session->getFlashBag()
                    ->add('alert', 'Erreur de lecture du fichier : '.$e ) ;
                 return  $this->redirectToRoute('easyadmin');
                 }
                 
               
                  
     }
     
      public function eraseBatchAction(array $ids)
    { $role=$roles=$this->getUser()->getRoles();
   
    If ($role[0]=='ROLE_SUPER_ADMIN'){
        
        $class = $this->entity['class'];
       
        $em=$this->getDoctrine()->getManager();
        
       
            $repository = $this->getDoctrine()->getRepository($class);
            $repositoryUser = $this->getDoctrine()->getRepository(User::class);
            $repositoryEleves = $this->getDoctrine()->getRepository(Elevesinter::class);
        foreach($ids as $id)
        
        { 
            $fichier=$repository->find(['id'=>intval($id)]);
            
            if ($fichier->getTypefichier()==6){
                $eleve=$repositoryEleves->findOneByAutorisationphotos(['autorisationphotos'=>$fichier]);
                if($eleve){
                $eleve->setAutorisationphotos(null);
                $em->persist($eleve);
                $em->flush();
                $fichier->setEquipe(null);
                $fichier->setEdition(null);
               }
               if(!$eleve){
                 $prof=$repositoryUser->findOneByAutorisationphotos(['autorisationphotos'=>$fichier]);
                 if ($prof){
                $prof->setAutorisationphotos(null);
                $em->persist($prof);
                $em->flush();
                 }
               }
                $em->remove($fichier);
                $em->flush();
      }  
     }
   }
         else{
             $this->session->getFlashBag()
                    ->add('info', 'Vous n\'avez pas l\'autorisation de tout effacer') ;
             
         }
  }
    
    
    
      public function deleteAction()
    { 
        $class = $this->entity['class'];
         $id = $this->request->query->get('id');
         
      
        $em=$this->getDoctrine()->getManager();
        
       
            $repository = $this->getDoctrine()->getRepository($class);
            $fichier=$repository->find(['id'=>$id]);
            $repositoryEleves = $this->getDoctrine()->getRepository(Elevesinter::class);
            $repositoryUser = $this->getDoctrine()->getRepository(User::class);
      
          
            
            if ($fichier->getTypefichier()==6){
                $eleve=$repositoryEleves->findOneByAutorisationphotos(['autorisationphotos'=>$fichier]);
                $prof= $repositoryUser->findOneByAutorisationphotos(['autorisationphotos'=>$fichier]);
           
               
                if (($eleve) and (!$prof)){
                   
                $eleve->setAutorisationphotos(null);
                
                
               $fichier->setEquipe(null);   
               $em->persist($eleve);
                 }
                   if ((!$eleve) and ($prof)){
                  
                $prof->setAutorisationphotos(null);
                $em->persist($prof);
                              
                 }
                $fichier->setEdition(null);
                $em->remove($fichier);
                $em->flush();
   
        }  
       
         if (($fichier->getTypefichier()==0) or ($fichier->getTypefichier()==1) or ($fichier->getTypefichier()==2) and ($fichier->getNational()==true)){
                    
             $fichier->setNational(false);
                    
        $em->persist($fichier);
        $em->flush();
        return $this->redirectToRoute('easyadmin');
         }
        
       return parent::deleteAction();
    }
    
    public function telechargerBatchAction(array $ids){
         $class = $this->entity['class'];
       
        $repository = $this->getDoctrine()->getRepository($class);
        
        $zipFile = new \ZipArchive();
        $now   = new \DateTime();
         $FileName= 'telechargement_olymphys_'.$now->format( 'd-m-Y\-His' );
       
         if ($zipFile->open($FileName, ZipArchive::CREATE) === TRUE)
        
        {
            foreach ($ids as $id) 
                {  
                    $entity = $repository->find(['id'=>intval($id)]);
                    $typefichier= $entity->getTypefichier();
                    
                   switch ($typefichier) {              
                       case 0 :     $fichier= $this->getParameter('app.path.fichiers').'/memoires/'.$entity->getFichier();
                           break;
                       case 1 :     $fichier= $this->getParameter('app.path.fichiers').'/memoires/'.$entity->getFichier();  
                           break;
                       case 2 :     $fichier= $this->getParameter('app.path.fichiers').'/resumes/'.$entity->getFichier();
                           break;
                       case 3 :     $fichier= $this->getParameter('app.path.fichiers').'/presentation/'.$entity->getFichier();
                           break;
                       case 4 :     $fichier= $this->getParameter('app.path.fichiers').'/fichessecur/'.$entity->getFichier();   
                           break;
                       case 5 :     $fichier= $this->getParameter('app.path.fichiers').'/diaporamas/'.$entity->getFichier();
                           break;
                       case 6 :     $fichier= $this->getParameter('app.path.fichiers').'/autorisations/'.$entity->getFichier();  
                           break;
                           
                }
                    try{
                   
                     $zipFile->addFromString(basename($fichier),  file_get_contents($fichier));//voir https://stackoverflow.com/questions/20268025/symfony2-create-and-download-zip-file
                    }
                    catch(\Exception $e){
                       
                      }
                    
                }
        
              $zipFile->close();
    }
    
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
          
          
          
          
    public function persistEntity($entity){
        $request=Request::createFromGlobals();
        
        
        
       $em=$this->getDoctrine()->getManager();
       $edition=$this->session->get('edition');
       $edition=$em->merge($edition);
       $entity->setEdition($edition);
     
       if ($request->query->get('entity')== 'Fichiersequipesautorisations'){
       $entity->setTypefichier(6);         
        $entity->setNational(0);
       $citoyen = $entity->getEleve();
      if ($citoyen){
          
          $entity->setEquipe($citoyen->getEquipe());
      }
      
       if(!$citoyen){
        $citoyen = $entity->getProf();
                
       } 
       $citoyen->setAutorisationphotos($entity);
      $entity->setNomautorisation($citoyen->getNom().'-'.$citoyen->getPrenom());
        }
           return parent::persistEntity($entity);
   
    }
         
          
          
}

