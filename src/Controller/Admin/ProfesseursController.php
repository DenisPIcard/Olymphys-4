<?php
namespace App\Controller\Admin;
use App\Form\Filter\ElevesinterFilterType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType ; 
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\Equipesadmin;
use App\Entity\Edition;
use App\Entity\Elevesinter;
use App\Entity\User;
use App\Entity\Rne;
use App\Form\Filter\ProfesseursFilterType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use PhpOffice\PhpSpreadsheet\Spreadsheet;



use EasyCorp\Bundle\EasyAdminBundle\Event\EasyAdminEvents;

class ProfesseursController extends EasyAdminController
{    private $session;
    public function __construct(SessionInterface $session)
        {
            $this->session = $session;
            
        }
    
    protected function createFiltersForm(string $entityName): FormInterface
    { 
        $form = parent::createFiltersForm($entityName);
        $form->add('edition', ProfesseursFilterType::class, [
                                        'class' => Edition::class,
                                        'query_builder' => function (EntityRepository $er) {
                                                return $er->createQueryBuilder('u')
                                                        ->addOrderBy('u.ed', 'DESC');
                                                     },
                                       'choice_label' => 'getEd',
                                       'multiple'=>false,
                                       'mapped'=>false,
            ]);


        return $form;
    }


    public  function createListQueryBuilder($entityClass, $sortDirection, $sortField = null, $dqlFilter = null){
        $request=Request::createFromGlobals();
        $repositoryEdition = $this->getDoctrine()->getRepository('App:Edition');
        $repositoryEquipes = $this->getDoctrine()->getRepository('App:Equipesadmin');
       if ($request->query->get('filters')==null){

           $edition= $this->session->get('edition');

       }
       else{

           $editionid = $request->query->get('filters')['edition'];
           $edition = $repositoryEdition->findOneBy(['id'=>$editionid]);
           $this->session->set('edition_titre',$edition->getEd() );
       }

        $listequipes= $repositoryEquipes->findBy(['edition'=>$edition]);


        $em = $this->getDoctrine()->getManagerForClass($this->entity['class']);

        $qb1 =$em->createQueryBuilder()
            ->select('entity')
            ->from($this->entity['class'], 'entity')
            ->groupBy('entity.user')
            ->join('entity.equipes','eq')
            ->andWhere('eq.edition =:edition')
            ->setParameter('edition', $edition)
            ->leftJoin('entity.user','u')
            ->orderBy('u.nom', 'ASC');
        $listProfs= $qb1->getQuery()->getResult();

        if($listProfs!=null){
            foreach($listProfs as $prof){
                $equipestring ='';
                $equipes=$repositoryEquipes->createQueryBuilder('e')
                                            ->where('e.edition =:edition')
                                            ->setParameter('edition',$edition)
                                            ->andWhere('e.idProf1 =:user OR e.idProf2 =:user')
                                            ->setParameter('user',$prof->getUser())
                                            ->getQuery()->getResult();

                    if ($equipes!=null){
                       foreach($equipes as $equipe){
                           if ($equipe->getIdProf1()==$prof->getUser()){
                                    $encad='(prof1)';
                                }
                                if ($equipe->getIdProf2()==$prof->getUser()){
                                    $encad='(prof2)';
                                }
                               $equipestring =  $equipestring.$equipe->getTitreProjet().$encad;
                               if (next($equipes)!=null){
                                   $equipestring= $equipestring.' || ';
                               }
                           }
                       $prof->setEquipesstring($equipestring);
                       $em->persist($prof);
                       $em->flush();
                    }
                }
        }
        return $qb1;

      }
    /**
     * @Route("/Professeurs/editer_tableau_excel,{choix}", name="list_prof_excel")
     */

    public function editer_tableau_excel($choix){


        $em = $this->getDoctrine()->getManager();
        $repositoryEdition = $this->getDoctrine()->getRepository('App:Edition');
        $repositoryEquipes = $this->getDoctrine()->getRepository('App:Equipesadmin');
        $edition=$repositoryEdition->findOneBy(['id'=>$choix]);
        $repositoryProfs = $this->getDoctrine()->getManager()->getRepository('App:Professeurs');

        $queryBuilder =  $repositoryProfs->createQueryBuilder('p')
            ->groupBy('p.user')
            ->leftJoin('p.equipes','eqs')
            ->andWhere('eqs.edition =:edition')
            ->setParameter('edition', $edition)
            ->leftJoin('p.user','u')
            ->orderBY('u.nom','ASC');
        $listProfs= $queryBuilder->getQuery()->getResult();

        if($listProfs!=null){
            foreach($listProfs as $prof){
                $equipestring ='';

                $equipes=$repositoryEquipes->createQueryBuilder('e')
                    ->where('e.edition =:edition')
                    ->setParameter('edition',$edition)
                    ->andWhere('e.idProf1 =:user OR e.idProf2 =:user')
                    ->setParameter('user',$prof->getUser())
                    ->getQuery()->getResult();

                if ($equipes!=null){
                    foreach($equipes as $equipe){
                        if ($equipe->getIdProf1()==$prof->getUser()){
                            $encad='(prof1)';
                        }
                        if ($equipe->getIdProf2()==$prof->getUser()){
                            $encad='(prof2)';
                        }
                        $equipestring =  $equipestring.$equipe->getTitreProjet().$encad;
                        if (next($equipes)!=null){
                            $equipestring=$equipestring."\n";
                        }
                    }
                    $equipestring=count($equipes).'-'.$equipestring;
                    $prof->setEquipesstring($equipestring);
                    $em->persist($prof);
                    $em->flush();
                }
            }
        }


        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator("Olymphys")
            ->setLastModifiedBy("Olymphys")
            ->setTitle("OdPF".$edition->getEd()."ème édition - professeurs encadrants")
            ->setSubject("PROFESSEURS")
            ->setDescription("Office 2007 XLSX Document pour comité")
            ->setKeywords("Office 2007 XLSX")
            ->setCategory("Test result file");

        $sheet = $spreadsheet->getActiveSheet();
        foreach(['A','B','C','D','E','F','G','H','I','J','K','L']as $letter){
            $sheet->getColumnDimension($letter)->setAutoSize(true);

        }
        $sheet->setCellValue('A1', 'Professeurs de la '.$edition->getEd().'e'.' édition');

        $ligne=2;


        $sheet->setCellValue('A'.$ligne, 'Nom')
            ->setCellValue('B'.$ligne, 'Prénom')
            ->setCellValue('C'.$ligne, 'Adresse')
            ->setCellValue('D'.$ligne, 'Ville')
            ->setCellValue('E'.$ligne, 'Code Postal')
            ->setCellValue('F'.$ligne, 'Courriel')
            ->setCellValue('G'.$ligne, 'téléphone')
            ->setCellValue('H'.$ligne, 'Code UAI')
            ->setCellValue('I'.$ligne, 'Lycée')
            ->setCellValue('J'.$ligne, 'Commune lycée')
            ->setCellValue('K'.$ligne, 'Académie')
            ->setCellValue('L'.$ligne, 'Equipes');;

        $ligne +=1;

        foreach ($listProfs as $prof)
        {


            $sheet->setCellValue('A'.$ligne,$prof->getUser()->getNom() )
                ->setCellValue('B'.$ligne, $prof->getUser()->getPrenom())
                ->setCellValue('C'.$ligne, $prof->getUser()->getAdresse())
                ->setCellValue('D'.$ligne, $prof->getUser()->getVille())
                ->setCellValue('E'.$ligne, $prof->getUser()->getCode())
                ->setCellValue('F'.$ligne, $prof->getUser()->getEmail())
                ->getCell('G'.$ligne)->setValueExplicit($prof->getUser()->getPhone(), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('H' . $ligne, $prof->getUser()->getRneId()->getRne())
                ->setCellValue('I' . $ligne, $prof->getUser()->getRneId()->getNom())
                ->setCellValue('J' . $ligne, $prof->getUser()->getRneId()->getCommune());
            $sheet->setCellValue('K'.$ligne, $prof->getUser()->getRneId()->getAcademie());

            $equipesstring=explode('-',$prof->getEquipesstring());
            $sheet ->getRowDimension($ligne)->setRowHeight(12.5*intval($equipesstring[0]));
            $sheet ->getCell('L'.$ligne)->setValueExplicit($equipesstring[1],\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);//'abc \n cde'
            $sheet->getStyle('A'.$ligne.':L'.$ligne)->getAlignment()->setWrapText(true);
            $ligne +=1;
        }




        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="professeurs.xls"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xls($spreadsheet);
        ob_end_clean();
        $writer->save('php://output');






    }



}

