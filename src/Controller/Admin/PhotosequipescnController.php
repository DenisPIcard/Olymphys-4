<?php
namespace App\Controller\Admin;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType ; 
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use App\Entity\Equipesadmin;
use App\Entity\Edition;
use App\Entity\Centrescia;
use App\Entity\Photos;
use EasyCorp\Bundle\EasyAdminBundle\Mapping\Annotation\Entity;
use App\Form\Filter\PhotosequipescnFilterType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use Symfony\Component\Filesystem\Filesystem;
use ZipArchive;

use EasyCorp\Bundle\EasyAdminBundle\Event\EasyAdminEvents;

class PhotosequipescnController extends EasyAdminController
{   public function __construct(SessionInterface $session)
                    {  
                        $this->session=$session;
                       
                    }
    protected function createFiltersForm(string $entityName): FormInterface
    { 
        $form = parent::createFiltersForm($entityName);
        
        $form->add('edition', PhotosequipescnFilterType::class, [
            'class' => Edition::class,
            'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('u')
                                    ->orderBy('u.ed', 'DESC');
                                     },
           'choice_label' => 'getEd',
            'multiple'=>false,]);
           
           $form->add('equipe', PhotosequipescnFilterType::class, [
                               'class' => Equipesadmin::class,
                               'query_builder' => function (EntityRepository $er) {
                                         return $er->createQueryBuilder('u')
                                                     ->andWhere('u.selectionnee =:selectionnee')
                                                     ->setParameter('selectionnee', TRUE)
                                                     ->addOrderBy('u.edition', 'DESC')
                                                     ->addOrderBy('u.lettre','ASC')
                                                 ;
                                                  },
                           'choice_label'=>'getInfoequipenat',
                         'multiple'=>false,]);
                                             
        return $form;
    }
    public function persistEntity($entity)
    {
                  $entity->setNational(True);
        
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
            ->join('entity.equipe','eq')
             ->andWhere('entity.edition =:edition')
            ->setParameter('edition', $edition)  
            ->addOrderBy('eq.lettre', 'ASC');
           
           
          if (!empty($dqlFilter)) {
              $queryBuilder->andWhere($dqlFilter);
                                          
              }
           
            return $queryBuilder;
         
      }
public function EnregistrerAction() {
 
         
         $repository = $this->getDoctrine()->getRepository(Photos::class);
         $id = $this->request->query->get('id');
         $entity = $repository->find($id);
         $fichier=$this->getParameter('app.path.photos').'/'.$entity->getPhoto();
         $application= 'image/jpeg';
         $name=$entity->getPhoto();
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
    public function deleteAction(){
          $filesystem = new Filesystem();
     $repositoryPhotoscn=$this->getDoctrine()->getRepository('App:Photos');
            $id= $this->request->query->get('id');
            $image= $repositoryPhotoscn->find(['id'=>$id]);
            $filesystem->remove('/upload/photos/thumbs/'.$image->getPhoto());
    return parent::deleteAction();
    
}
public function telechargerBatchAction(array $ids)
    {
        $class = $this->entity['class'];
       
        $repository = $this->getDoctrine()->getRepository($class);
        $zipFile = new \ZipArchive();
        
            $FileName=$this->session->get('edition')->getEd().'photoscn'.date('Y-m-d-H-i-s');
        
        
        if ($zipFile->open($FileName, ZipArchive::CREATE) === TRUE)
        {
            foreach ($ids as $id) 
                {


                    $entity = $repository->find($id);
                   
                       
                    $fichier= $this->getParameter('app.path.photos').'/'.$entity->getPhoto();
                    
                    //$nom_memoire=$entity->getMemoire();
                    //$filenameFallback = iconv('UTF-8','ASCII//TRANSLIT',$nom_memoire);
                    $zipFile->addFromString(basename($fichier),  file_get_contents($fichier));//voir https://stackoverflow.com/questions/20268025/symfony2-create-and-download-zip-file

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
                //$content = $this->render('secretariat\lire_memoire.html.twig', array('repertoirememoire' => $this->getParameter('repertoire_memoire_national'),'memoire'=>$fichier));
                return $response; 
    } 




    
}

