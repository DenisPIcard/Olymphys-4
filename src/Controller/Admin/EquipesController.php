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
use App\Form\Filter\EquipesFilterType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;

use EasyCorp\Bundle\EasyAdminBundle\Event\EasyAdminEvents;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\RichText\Run;

class EquipesController extends EasyAdminController
{   
    public function __construct(SessionInterface $session)
        {
            $this->session = $session;
            
        }
   
     

    protected function createFiltersForm(string $entityName): FormInterface
    { 
        $form = parent::createFiltersForm($entityName);
        
        $form->add('edition', EquipesFilterType::class, [
            'class' => Edition::class,
            'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('e')
                                           ->addOrderBy('e.ed', 'DESC');
                                     },
           'choice_label' => 'getEd',
            'multiple'=>false,
            'mapped'=>false]);
            
           
        return $form;
    }
   
        
  
    public  function createListQueryBuilder($entityClass, $sortDirection, $sortField = null, $dqlFilter = null){
           $request=Request::createFromGlobals();
        
        $edition= $this->session->get('edition');
         $this->session->set('edition_titre',$edition->getEd());
            $em = $this->getDoctrine()->getManagerForClass($this->entity['class']);
    
        /* @var DoctrineQueryBuilder */
        $queryBuilder = $em->createQueryBuilder()
            ->select('entity')
            ->from($this->entity['class'], 'entity')
            ->leftJoin('entity.infoequipe','e')
            ->where('e.edition =:edition')
            ->setParameter('edition', $this->session->get('edition'))
           ->addOrderBy('entity.lettre', 'ASC')
           ->addOrderBy('entity.'.$sortField,$sortDirection);
        
     
        
    
           
           
           
            return $queryBuilder;
         
      }
  
  public function extractionTableauPourMailingJuresBatchAction(){//Pour obtenir le tableau une ligne par juré pour chacune de ses équipes pour tous les jurés
       // 1	    2   	     3	     4	                                  5	       6	                 7	8	9	               10	11	                  12	13	14	15	               16	    17	                    18	    19	                   20	                     21	     22	                       23
   // horairel lettre	sous-jury	interlocuteur	courrielinterlocuteur	telinterlocuteur	hote	lien	lien_principal	code	lien_secours	sujet	lycée	civilité	prénom_prof1	nom_prof1	courriel_prof1	Tél_prof1	 nom_prof 2	courriel_hote	telhote	observateur	Courielcomit
   
      
      
      $edition=$this->session->get('edition');
         $class = $this->entity['class'];
        $repositoryJures = $this->getDoctrine()->getRepository('App:Jures');
        $repositoryUser = $this->getDoctrine()->getRepository('App:User');
        $jures=$repositoryJures->findAll();
        $repository = $this->getDoctrine()->getRepository($class);
        $em = $this->getDoctrine()->getManagerForClass($this->entity['class']);
        /* @var DoctrineQueryBuilder */
        $queryBuilder = $em->createQueryBuilder()
                ->select('entity')
                -> from($this->entity['class'], 'entity')
                ->leftJoin('entity.infoequipe','eq')
                ->andWhere('eq.selectionnee = TRUE')
                ->andWhere('eq.edition =:edition')
                ->setParameter('edition',$edition)
                ->orderBy('eq.lettre','ASC');
                
        $liste_equipes = $queryBuilder->getQuery()->getResult();
        $spreadsheet = new Spreadsheet();
         $spreadsheet->getProperties()
                        ->setCreator("Olymphys")
                        ->setLastModifiedBy("Olymphys")
                        ->setTitle("CN  ".$edition->getEd()."ème édition -Mailing Jurés")
                        ->setSubject("Mailing Jurés ")
                        ->setDescription("Office 2007 XLSX Document pour mailing diplomes participation ")
                        ->setKeywords("Office 2007 XLSX")
                        ->setCategory("Test result file");
 
                $sheet = $spreadsheet->getActiveSheet();
 
               
           
       
                $ligne=1;

                $sheet
                    ->setCellValue('A'.$ligne, 'Nom')
                    ->setCellValue('B'.$ligne, 'Prenom')
                    ->setCellValue('C'.$ligne, 'mail')
                    ->setCellValue('D'.$ligne, 'lettre')
                    ->setCellValue('E'.$ligne, 'sujet')
                    ->setCellValue('F'.$ligne, 'lycée')
                    ->setCellValue('G'.$ligne, 'ville')    
                    ->setCellValue('H'.$ligne, 'salle_principale')
                    ->setCellValue('I'.$ligne, 'code_principal')    
                    ->setCellValue('J'.$ligne, 'salle_secours')
                    ->setCellValue('K'.$ligne, 'code_secours')   
                    ->setCellValue('L'.$ligne, 'prof_1')
                    ->setCellValue('M'.$ligne, 'tel_prof1')  
                    ->setCellValue('N'.$ligne, 'courriel_prof1')
                   ->setCellValue('O'.$ligne, 'prof_2')
                   ->setCellValue('P'.$ligne, 'hote')  
                   ->setCellValue('Q'.$ligne, 'tel_hote')
                   ->setCellValue('R'.$ligne, 'courriel_hote')
                   ->setCellValue('S'.$ligne, 'observateur')
                   ->setCellValue('T'.$ligne, 'nom_Interlocuteur')
                   ->setCellValue('U'.$ligne, 'tel_Interlocuteur')
                   ->setCellValue('V'.$ligne, 'courriel_Interlocuteur') 
                   ->setCellValue('W'.$ligne, 'horaire') 
                   ->setCellValue('X'.$ligne, 'jury') 
                   ->setCellValue('Y'.$ligne, 'president') ;
                $ligne +=1; 
        foreach ($jures as $jure){
             $qb=$repositoryUser->createQueryBuilder('p');
           $jure_user= $repositoryUser ->createQueryBuilder('j')
                                                  ->select('j')
                                                  ->where('j.nom =:nom')
                                                  ->setParameter('nom',$jure->getNomJure())
                                                  ->andWhere('j.prenom =:prenom')
                                                  ->setParameter('prenom',$jure->getPrenomJure())
                                                  ->getQuery()->getResult();
            If(count( $jure_user) >1){
               foreach ($jure_user as $juryuser){
                   
                   if ($juryuser->getRoles()[0]=='ROLE_JURY'){
                       
                       $mail= $juryuser->getEmail();
                   }
            }
            }
          else{
          $mail= $jure_user[0]->getEmail();
          
          }
         $attrib=$jure->getAttributions() ;
            //foreach($liste_equipes as $equipe)
             foreach($attrib as $key => $value)
            {
                               
                                 $method = 'get'.ucfirst($key); 
	                $statut = $jure->$method();
			
				 /*if (($statut=='0')or ($statut =='1')and ($statut!=null)) 
				{   */
                     $equipe=$repository->createQueryBuilder('e')
                                                               ->andWhere('e.lettre =:lettre')
                                                               ->setParameter('lettre',$key)
                                                               ->getQuery()->getSingleResult();               
                     
                    $idprof1=$equipe->getInfoequipe()->getIdProf1();
                    $idprof2=$equipe->getInfoequipe()->getIdProf2();
                   $prof1=$repositoryUser->findOneById(['id'=>$idprof1]);
                   $prof2=$repositoryUser->findOneById(['id'=>$idprof2]);
               
                    $sheet->setCellValue('A'.$ligne,$jure->getNomJure() )
                        ->setCellValue('B'.$ligne, $jure->getPrenomJure())
                        ->setCellValue('C'.$ligne, $mail)
                        ->setCellValue('D'.$ligne, $equipe->getInfoequipe()->getLettre())
                        ->setCellValue('E'.$ligne,$equipe->getInfoequipe()->getTitreProjet())
                        ->setCellValue('F'.$ligne,$equipe->getInfoequipe()->getNomLycee())                           
                        ->setCellValue('G'.$ligne,$equipe->getInfoequipe()->getRneId()->getCommune())
                        ->setCellValue('H'.$ligne,$equipe->getSalle() );
                           //dd(substr(, 0,44))
                            if (substr($equipe->getSalle(), 0,45) ==' https://gouv1.rendez-vous.renater.fr/odpf-28'){
                            $sheet ->setCellValue('K'.$ligne,$equipe->getCode() );
                            }
                            else{
                          $sheet->setCellValue('I'.$ligne,$equipe->getCode() );}
                        $sheet->setCellValue('J'.$ligne, $equipe->getSallesecours())
                        ->setCellValue('L'.$ligne, $prof1->getPrenomNom())
                        ->setCellValue('M'.$ligne, $prof1->getPhone())
                        ->setCellValue('N'.$ligne,$prof1->getEmail());
                            if($prof2!=null){
                         $sheet->setCellValue('O'.$ligne, $prof2->getPrenomNom());
                        //->setCellValue('G'.$ligne, $prof2->getPhone())
                       // ->setCellValue('H'.$ligne,$prof2->getEmail());
                            }
                         $sheet->setCellValue('P'.$ligne, $equipe->getHote()->getPrenomNom())  
                        ->setCellValue('Q'.$ligne, $equipe->getHote()->getPhone())
                        ->setCellValue('R'.$ligne, $equipe->getHote()->getEmail())
                        ->setCellValue('S'.$ligne, $equipe->getObservateur()->getPrenomNom())
                        ->setCellValue('T'.$ligne, $equipe->getInterlocuteur()->getPrenomNom())
                        ->setCellValue('U'.$ligne, $equipe->getInterlocuteur()->getPhone())
                        ->setCellValue('V'.$ligne, $equipe->getInterlocuteur()->getEmail())
                        ->setCellValue('W'.$ligne, $equipe->getHeure());
                         
                        if($equipe->getInterlocuteur()->getPrenomNom() == 'Valérie Belle '){
                            $sheet->setCellValue('X'.$ligne, 'P') 
                             ->setCellValue('Y'.$ligne, 'présidente') ;
                           }         
                         
                          if($equipe->getInterlocuteur()->getPrenomNom() == 'Guy Bouyrie '){ 
                              $sheet->setCellValue('X'.$ligne, 'VP') 
                              ->setCellValue('Y'.$ligne, 'vice-président') ;  
                             
                         }        
                          if($equipe->getLettre()=='W')      {
                              $sheet->setCellValue('X'.$ligne, 'complet') ;
                              
                          } 
                                 
                      $ligne +=1;
                                         
                                     }
                                 }
                                
           
 
               
 
                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename="mailing_visio_jures_CN.xls"');
                header('Cache-Control: max-age=0');
                $writer = new Xlsx($spreadsheet);
                //$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xls($spreadsheet);
                $writer->save('php://output');
                //$writer->save('professeurs.xls');
          
          
          
          
          
          
          
          
          
      }       
    
}

