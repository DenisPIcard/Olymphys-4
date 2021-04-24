<?php
namespace App\Controller\Admin;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType ; 
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\FormInterface;
use App\Entity\Equipesadmin;
use App\Entity\Edition;
use App\Entity\Centrescia;
use App\Entity\Elevesinter;
use App\Entity\Eleves;
use App\Entity\Equipes;
use App\Entity\Fichiersequipes;
use App\Entity\User;
use App\Form\Filter\EquipesadminFilterType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer as Writer;
use EasyCorp\Bundle\EasyAdminBundle\Event\EasyAdminEvents;

class EquipesadminController extends EasyAdminController
{   
    public function __construct(SessionInterface $session)
        {
            $this->session = $session;
            
        }
   
     

    protected function createFiltersForm(string $entityName): FormInterface
    { 
        $form = parent::createFiltersForm($entityName);
        
        $form->add('edition', EquipesadminFilterType::class, [
            'class' => Edition::class,
            'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('u')
                                    ->addOrderBy('u.ed', 'DESC');
                                     },
           'choice_label' => 'getEd',
            'multiple'=>false,]);
            $form->add('centre', EquipesadminFilterType::class, [
                         'class' => Centrescia::class,
                         'query_builder' => function (EntityRepository $er) {
                                         return $er->createQueryBuilder('u')
                                                 ->addOrderBy('u.centre', 'ASC');

                                                  },
                        'choice_label' => function($centre){return $centre->getCentre();},
                         'multiple'=>false,]);
           
        return $form;
    }
    public function persistEntity($entity)
    {
        
        $repositoryEdition = $this->getDoctrine()->getRepository('App:Edition');
                  $edition=$repositoryEdition->findOneBy([], ['id' => 'desc']);
                  $entity->setEdition($edition);
        
         parent::persistEntity($entity);
        
    }
    public  function createListQueryBuilder($entityClass, $sortDirection, $sortField = null, $dqlFilter = null){
           $request=Request::createFromGlobals();
        
        $edition= $this->session->get('edition');
         $this->session->set('edition_titre',$edition->getEd());
            $em = $this->getDoctrine()->getManagerForClass($this->entity['class']);
      if ($request->query->get('entity')=='Equipesadmin'){
        /* @var DoctrineQueryBuilder */
        $queryBuilder = $em->createQueryBuilder()
            ->select('entity')
            ->from($this->entity['class'], 'entity')
            ->leftJoin('entity.edition','edition')
            ->where('edition.ed =:edition')
            ->setParameter('edition', $edition->getEd())
           ->addOrderBy('entity.centre', 'ASC')
           ->addOrderBy('entity.'.$sortField,$sortDirection);}
         if ($request->query->get('entity')=='Selectionnees'){
             $queryBuilder = $em->createQueryBuilder()
            ->select('entity')
            ->from($this->entity['class'], 'entity')
            ->where('entity.edition =:edition')
            ->setParameter('edition', $edition)
            ->andWhere('entity.selectionnee = TRUE')
           ->addOrderBy('entity.lettre', 'ASC');
            
         }
           
           
           
            return $queryBuilder;
         
      }
    public function deleteAction(){
         $class = $this->entity['class'];
       $id = $this->request->query->get('id');
           $em=$this->getDoctrine()->getManager();   
        $repository = $this->getDoctrine()->getRepository($class);
        $equipe=$repository->find(['id'=>$id]);
       
         $repositoryElevesinter = $this->getDoctrine()->getRepository(Elevesinter::class);
         $repositoryEleves = $this->getDoctrine()->getRepository(Eleves::class);
         $repositoryFichiers = $this->getDoctrine()->getRepository(Fichiersequipes::class);
         $repositoryEquipes = $this->getDoctrine()->getRepository(Equipes::class);
         $qb= $repositoryFichiers->createQueryBuilder('f')
                 ->where('f.equipe =:equipe')
                 ->setParameter('equipe',$equipe);
        $liste_fichiers=$qb->getQuery()->getResult();
        
        foreach($liste_fichiers as $fichier){
            $fichier->setEquipe(null);
            $fichier->setProf(null);
            $fichier->setEleve(null);
         $em->remove($fichier);
        }
         $qb2= $repositoryElevesinter->createQueryBuilder('e')
                 ->andWhere('e.equipe =:equipe')
                 ->setParameter('equipe',$equipe);
        $liste_elevesinter=$qb2->getQuery()->getResult();
        
        $liste_eleves=$repositoryEleves->createQueryBuilder('e')
                 ->andWhere('e.equipe =:equipe')
                 ->setParameter('equipe',$equipe)
                ->getQuery()->getResult();
        $equipe=$repositoryEquipes->createQueryBuilder('e')
                 ->andWhere('e.infoequipe =:equipe')
                 ->setParameter('equipe',$equipe)
                ->getQuery()->getSingleResult();
       If ($equipe){
        $equipe->setInfoequipe(null);
       }
         foreach($liste_elevesinter as $eleve){
            $eleve->setEquipe(null);
            $eleve->setAutorisationphotos(null);
             $em->remove($eleve);      
        }
         foreach($liste_eleves as $eleve){
            $eleve->setEquipe(null);
           
             $em->remove($eleve);      
        }
        
        
        
        
        $em->flush();
        
        
        
        
        
        return parent::deleteAction(); 
    }
  
    function extractionprofsequipescnBatchAction(){
         $edition=$this->session->get('edition');
         $class = $this->entity['class'];
        $repositoryProf = $this->getDoctrine()->getRepository('App:User');
        $repository = $this->getDoctrine()->getRepository($class);
        $em = $this->getDoctrine()->getManagerForClass($this->entity['class']);
        /* @var DoctrineQueryBuilder */
        $queryBuilder = $em->createQueryBuilder()
                ->select('entity')
                -> from($this->entity['class'], 'entity')
                ->andWhere('entity.selectionnee = TRUE')
                ->andWhere('entity.edition =:edition')
                ->setParameter('edition',$edition)
                ->orderBy('entity.lettre','ASC');
                
        $liste_equipes = $queryBuilder->getQuery()->getResult();
             
        //dump($liste_equipes);
        //dd($edition);
        $spreadsheet = new Spreadsheet();
         $spreadsheet->getProperties()
                        ->setCreator("Olymphys")
                        ->setLastModifiedBy("Olymphys")
                        ->setTitle("CN  ".$edition->getEd()."ème édition -Tableau destiné au jury")
                        ->setSubject("Tableau destiné au jury")
                        ->setDescription("Office 2007 XLSX Document pour mailing  ")
                        ->setKeywords("Office 2007 XLSX")
                        ->setCategory("Test result file");
 
                $sheet = $spreadsheet->getActiveSheet();
 
               
           
       
                $ligne=1;

                $sheet->setCellValue('A'.$ligne, 'Lettre')
                    ->setCellValue('B'.$ligne, 'Projet')
                    ->setCellValue('C'.$ligne, 'Prof 1')
                     ->setCellValue('D'.$ligne, 'tel prof1')  
                    ->setCellValue('E'.$ligne, 'mail prof1')
                   ->setCellValue('F'.$ligne, 'Prof2')
                    ->setCellValue('G'.$ligne, 'tel prof2')  
                    ->setCellValue('H'.$ligne, 'mail prof2')
                     ->setCellValue('G'.$ligne, 'lien_principal')
                       ->setCellValue('H'.$ligne, 'code_principal')
                         ->setCellValue('I'.$ligne, 'lien_secours')
                       ->setCellValue('J'.$ligne, 'code_secours')  
                             ;
                
                $ligne +=1; 

        	foreach ($liste_equipes as $equipe) 
                {
                    $idprof1=$equipe->getIdProf1();
                    $idprof2=$equipe->getIdProf2();
                   $prof1=$repositoryProf->findOneById(['id'=>$idprof1]);
                   $prof2=$repositoryProf->findOneById(['id'=>$idprof2]);
                    $sheet->setCellValue('A'.$ligne,$equipe->getLettre() )
                        ->setCellValue('B'.$ligne, $equipe->getTitreProjet())
                        ->setCellValue('C'.$ligne, $prof1->getNomPrenom())
                        ->setCellValue('D'.$ligne, $prof1->getPhone())
                        ->setCellValue('E'.$ligne,$prof1->getEmail());
                            if($prof2!=null){
                         $sheet->setCellValue('F'.$ligne, $prof2->getNomPrenom())
                        ->setCellValue('G'.$ligne, $prof2->getPhone())
                        ->setCellValue('H'.$ligne,$prof2->getEmail());
                         
                         
                         
                         
                            }
                      $ligne +=1;
                }
                    
 
               
 
                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename="professeurs_CN.xls"');
                header('Cache-Control: max-age=0');
                $writer = new Xlsx($spreadsheet);
                //$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xls($spreadsheet);
                $writer->save('php://output');
                $writer->save('professeurs.xls');
        
    }
    
    
    
    
    
    
}

