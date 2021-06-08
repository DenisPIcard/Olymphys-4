<?php
namespace App\Controller\Admin;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\Equipesadmin;
use App\Entity\Edition;
use App\Form\Filter\ElevesinterFilterType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


use EasyCorp\Bundle\EasyAdminBundle\Event\EasyAdminEvents;

class ElevesciaController extends EasyAdminController
{    private $session;
    public function __construct(SessionInterface $session)
        {
            $this->session = $session;
            
        }
    
    protected function createFiltersForm(string $entityName): FormInterface
    { 
        $form = parent::createFiltersForm($entityName);
        
        $form->add('edition', ElevesinterFilterType::class, [
            'class' => Edition::class,
            'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('u')
                                    ->orderBy('u.ed', 'DESC');
                                     },
           'choice_label' => 'getEd',
            'multiple'=>false,]);
            $form->add('equipe', ElevesinterFilterType::class, [
                         'class' => Equipesadmin::class,
                         'query_builder' => function (EntityRepository $er) {
                                         return $er->createQueryBuilder('u')
                                                        ->addOrderBy('u.edition','DESC')
                                                         ->addOrderBy('u.numero', 'ASC');

                                                  },
                        'choice_label' => function($equipe){return $equipe->getInfoequipe();},
                         'multiple'=>false,]);
           
        return $form;
    }
   
    public  function createListQueryBuilder($entityClass, $sortDirection, $sortField = null, $dqlFilter = null){
           $repositoryEdition = $this->getDoctrine()->getRepository('App:Edition');
            $edition= $this->session->get('edition');
                 // $edition=$repositoryEdition->findOneBy([], ['id' => 'desc']);
            $em = $this->getDoctrine()->getManagerForClass($this->entity['class']);
        /* @var DoctrineQueryBuilder */
        $queryBuilder = $em->createQueryBuilder()
            ->select('entity')
            ->from($this->entity['class'], 'entity')
            ->leftJoin('entity.equipe','equipe')
            ->where('equipe.edition =:edition')
            ->setParameter('edition', $edition)
           ->orderBy('equipe.numero', 'ASC');
            return $queryBuilder;
         
      }
    
    public function extract_tableau_excel_ElevesnsBatchAction(){
        $repositoryEdition = $this->getDoctrine()->getRepository('App:Elevesinter');
            $edition= $this->session->get('edition');
                 // $edition=$repositoryEdition->findOneBy([], ['id' => 'desc']);
            $em = $this->getDoctrine()->getManagerForClass($this->entity['class']);
        /* @var DoctrineQueryBuilder */
        $queryBuilder = $em->createQueryBuilder()
                 ->select('entity')
                -> from($this->entity['class'], 'entity')
                ->leftJoin('entity.equipe','e')
                ->andWhere('e.selectionnee = FALSE')
                ->andWhere('e.edition =:edition')
                ->setParameter('edition',$edition);
        $liste_eleves = $queryBuilder->getQuery()->getResult();
             
        
        $spreadsheet = new Spreadsheet();
         $spreadsheet->getProperties()
                        ->setCreator("Olymphys")
                        ->setLastModifiedBy("Olymphys")
                        ->setTitle("CIA  ".$edition->getEd()."ème édition - élèves non sélectionnés")
                        ->setSubject("Elèves non sélectionnés")
                        ->setDescription("Office 2007 XLSX Document pour mailing diplomes participation ")
                        ->setKeywords("Office 2007 XLSX")
                        ->setCategory("Test result file");
 
                $sheet = $spreadsheet->getActiveSheet();
                foreach(['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','R','S','T']as $letter) {
                    $sheet->getColumnDimension($letter)->setAutoSize(true);
                }
               
           
       
                $ligne=1;

                $sheet->setCellValue('A'.$ligne, 'Nom')
                    ->setCellValue('B'.$ligne, 'Prenom')
                    ->setCellValue('C'.$ligne, 'N° equipe')
                    ->setCellValue('D'.$ligne, 'Titre')
                    ->setCellValue('E'.$ligne, 'Lycée')
                    ->setCellValue('F'.$ligne, 'Commune')
                    ->setCellValue('G'.$ligne, 'Courriel');
                
                $ligne +=1; 

        	foreach ($liste_eleves as $eleve) 
                {
                    $equipe=$eleve->getEquipe();
                 
                    $sheet->setCellValue('A'.$ligne,$eleve->getNom() )
                        ->setCellValue('B'.$ligne, $eleve->getPrenom())
                        ->setCellValue('C'.$ligne, $equipe->getNumero())
                        ->setCellValue('D'.$ligne, $equipe->getTitreProjet())
                        ->setCellValue('E'.$ligne,$equipe->getRneId()->getNom())
                        ->setCellValue('F'.$ligne, $equipe->getRneId()->getCommune())
                        ->setCellValue('G'.$ligne, $eleve->getCourriel());
                      $ligne +=1;
                }
                    
 

 
                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename="eleves_non_sélectionnés.xls"');
                header('Cache-Control: max-age=0');
        
                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xls($spreadsheet);
                ob_end_clean();
                $writer->save('php://output');
        
        
        
        
        
    }
    public function extract_tableau_excel_Eleves_sBatchAction(){
        $repositoryEdition = $this->getDoctrine()->getRepository('App:Elevesinter');
        $repositoryEquipescn = $this->getDoctrine()->getRepository('App:Equipes');
            $edition= $this->session->get('edition');
                 // $edition=$repositoryEdition->findOneBy([], ['id' => 'desc']);
            $em = $this->getDoctrine()->getManagerForClass($this->entity['class']);
        /* @var DoctrineQueryBuilder */
        $queryBuilder = $em->createQueryBuilder()
                 ->select('entity')
                -> from($this->entity['class'], 'entity')
                ->leftJoin('entity.equipe','e')
                ->andWhere('e.selectionnee = TRUE')
                ->andWhere('e.edition =:edition')
                ->setParameter('edition',$edition);
        $liste_eleves = $queryBuilder->getQuery()->getResult();
             
        
        $spreadsheet = new Spreadsheet();
         $spreadsheet->getProperties()
                        ->setCreator("Olymphys")
                        ->setLastModifiedBy("Olymphys")
                        ->setTitle("CN   ".$edition->getEd()."ème édition - élèves sélectionnés avec mail")
                        ->setSubject("Elèves  sélectionnés")
                        ->setDescription("Office 2007 XLSX Document pour mailing diplomes participation ")
                        ->setKeywords("Office 2007 XLSX")
                        ->setCategory("Test result file");
 
                $sheet = $spreadsheet->getActiveSheet();
                foreach(['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','R','S','T']as $letter) {
                    $sheet->getColumnDimension($letter)->setAutoSize(true);
                }
               
           
       
                $ligne=1;

                $sheet->setCellValue('A'.$ligne, 'Nom')
                    ->setCellValue('B'.$ligne, 'Prenom')
                    ->setCellValue('C'.$ligne, 'courriel')    
                    ->setCellValue('D'.$ligne, 'Lettre')
                     ->setCellValue('E'.$ligne, 'Titre')  
                    ->setCellValue('F'.$ligne, 'Lycée')
                   ->setCellValue('G'.$ligne, 'Commune')
                   ->setCellValue('G'.$ligne, 'prix');
                   
                
                $ligne +=1; 

        	foreach ($liste_eleves as $eleve) 
                {
                    $equipe=$eleve->getEquipe();
                    $equipecn=$repositoryEquipescn->createQueryBuilder('e')
                                                                         ->where('e.infoequipe =:equipe')
                                                                         ->setParameter('equipe',$equipe)
                                                                         ->getQuery()->getSingleResult();
                   
                    $sheet->setCellValue('A'.$ligne,$eleve->getNom() )
                        ->setCellValue('B'.$ligne, $eleve->getPrenom())
                        ->setCellValue('C'.$ligne, $eleve->getCourriel())    
                        ->setCellValue('D'.$ligne, $equipe->getLettre())
                        ->setCellValue('E'.$ligne, $equipe->getTitreProjet())
                        ->setCellValue('F'.$ligne,$equipe->getRneId()->getNom())
                        ->setCellValue('G'.$ligne, $equipe->getRneId()->getCommune())
                        ->setCellValue('H'.$ligne, $equipecn->getPrix()->getClassement())
                        ->setCellValue('I'.$ligne, $equipecn->getPrix()->getPrix())
                        ->setCellValue('J'.$ligne, $equipecn->getPhrases()->getPrix());
                      $ligne +=1;
                }
                    
 

 
                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename="eleves_sélectionnés.xls"');
                header('Cache-Control: max-age=0');
        
                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xls($spreadsheet);
                ob_end_clean();
                $writer->save('php://output');
        
        
        
        
        
    }

    /**
     * @Route("/Admin/extract_tableau_excel_Eleves,{editionid}", name="liste_eleves")
     */

    public function extract_tableau_excel_Eleves($editionid)
    {

        $repositoryEdition = $this->getDoctrine()->getRepository('App:Edition');
        //$edition= $this->session->get('edition');
        $edition=$repositoryEdition->findOneBy(['id'=>$editionid]);
        $elevesRepository = $this->getDoctrine()->getManager()->getRepository('App:Elevesinter');
        /* @var DoctrineQueryBuilder */
        $queryBuilder =  $elevesRepository->createQueryBuilder('el')
            ->select('el')
            ->leftJoin('el.equipe','e')
            ->andWhere('e.edition =:edition')
            ->setParameter('edition',$edition);
        $liste_eleves = $queryBuilder->getQuery()->getResult();


        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator("Olymphys")
            ->setLastModifiedBy("Olymphys")
            ->setTitle("CIA  ".$edition->getEd()."ème édition - élèves non sélectionnés")
            ->setSubject("Elèves non sélectionnés")
            ->setDescription("Office 2007 XLSX Document pour mailing diplomes participation ")
            ->setKeywords("Office 2007 XLSX")
            ->setCategory("Test result file");

        $sheet = $spreadsheet->getActiveSheet();
        foreach(['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','R','S','T']as $letter) {
            $sheet->getColumnDimension($letter)->setAutoSize(true);
        }



        $ligne=1;

        $sheet->setCellValue('A'.$ligne, 'Nom')
            ->setCellValue('B'.$ligne, 'Prenom')
            ->setCellValue('C'.$ligne, 'Numequipe')
            ->setCellValue('D'.$ligne, 'Titre')
            ->setCellValue('E'.$ligne, 'Lycée')
            ->setCellValue('F'.$ligne, 'Commune')
            ->setCellValue('G'.$ligne, 'Courriel');

        $ligne +=1;

        foreach ($liste_eleves as $eleve)
        {
            $equipe=$eleve->getEquipe();

            $sheet->setCellValue('A'.$ligne,$eleve->getNom() )
                ->setCellValue('B'.$ligne, $eleve->getPrenom())
                ->setCellValue('C'.$ligne, $equipe->getNumero())
                ->setCellValue('D'.$ligne, $equipe->getTitreProjet())
                ->setCellValue('E'.$ligne,$equipe->getRneId()->getNom())
                ->setCellValue('F'.$ligne, $equipe->getRneId()->getCommune())
                ->setCellValue('G'.$ligne, $eleve->getCourriel());
            $ligne +=1;
        }




        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="eleves_non_sélectionnés.xls"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xls($spreadsheet);
        ob_end_clean();
        $writer->save('php://output');





    }







}

