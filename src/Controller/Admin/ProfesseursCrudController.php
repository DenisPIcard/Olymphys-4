<?php

namespace App\Controller\Admin;

use App\Entity\Professeurs;
use App\Controller\Admin\Filter\CustomEditionFilter;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Provider\AdminContextProvider;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\String\UnicodeString;
use Symfony\Component\Routing\Annotation\Route;

class ProfesseursCrudController extends AbstractCrudController
{

    private $requestStack;
    private $adminContextProvider;

    public function __construct(RequestStack $requestStack, AdminContextProvider $adminContextProvider)
    {
        $this->requestStack = $requestStack;;
        $this->adminContextProvider = $adminContextProvider;

    }

    public static function getEntityFqcn(): string
    {
        return Professeurs::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        $session=$this->requestStack->getSession();
        $exp = new UnicodeString('<sup>e</sup>');
        $repositoryEdition=$this->getDoctrine()->getManager()->getRepository('App:Edition');
        $editionEd=$session->get('edition')->getEd();

        $crud->setPageTitle('index', 'Liste des professeurs de la ' . $editionEd . $exp .' édition ');
        if (isset($_REQUEST['filters']['edition'])){
            $editionId=$_REQUEST['filters']['edition'];
            $editionEd=$repositoryEdition->findOneBy(['id'=>$editionId]);
            $crud->setPageTitle('index', 'Liste des professeurs de la ' . $editionEd . $exp .' édition ');
        }
        return $crud
            ->setPageTitle(Crud::PAGE_DETAIL, 'Professeur')
            ->setSearchFields(['id', 'lettre', 'numero', 'titreProjet', 'nomLycee', 'denominationLycee', 'lyceeLocalite', 'lyceeAcademie', 'prenomProf1', 'nomProf1', 'prenomProf2', 'nomProf2', 'rne', 'contribfinance', 'origineprojet', 'recompense', 'partenaire', 'description'])
            ->setPaginatorPageSize(50);
            //->overrideTemplates(['layout' => 'bundles/EasyAdminBundle/list_profs.html.twig',]);


    }

    public function configureActions(Actions $actions): Actions
    {
        $session=$this->requestStack->getSession();
        $editionId = $session->get('edition')->getId();

        if (isset($_REQUEST['filters']['edition'])){

            $editionId=$_REQUEST['filters']['edition'];
                   }


        $tableauexcel = Action::new('profs_tableau_excel', 'Créer un tableau excel des professeurs','fas fa-columns')
            // if the route needs parameters, you can define them:
            // 1) using an array
            ->linkToRoute('profs_tableau_excel', ['idEdition' => $editionId, 'selectionnes'=>false])
            ->createAsGlobalAction();
            //->displayAsButton()->setCssClass('btn btn-primary');
        $tableauexcelselectionnes = Action::new('profs_tableau_excel_selectionnes', 'Créer un tableau excel des professeurs sélectionnés','fas fa-columns')
            // if the route needs parameters, you can define them:
            // 1) using an array
            ->linkToRoute('profs_tableau_excel', ['idEdition' => $editionId, 'selectionnes'=>true])
            ->createAsGlobalAction();
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $tableauexcel)
            ->add(Crud::PAGE_INDEX,$tableauexcelselectionnes)
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_INDEX, Action::EDIT)
            ->remove(Crud::PAGE_DETAIL, Action::EDIT)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_DETAIL, Action::DELETE);

    }

    public function configureFields(string $pageName): iterable
    {
        $nom = IntegerField::new('user.nom', 'nom');
        $prenom = TextField::new('user.prenom','Prénom');
        $nomLycee = TextField::new('user.rneId.nom', 'Lycée');
        $lyceeLocalite = TextField::new('user.rneId.commune', 'Ville');
        $lyceeAcademie = TextField::new('user.rneId.academie', 'Académie');
        $rne = TextField::new('user.rne', 'Code UAI');
        $equipes = IntegerField::new('equipesstring', 'Equipes');
        $tel=TextField::new('user.phone','téléphone');
        if (Crud::PAGE_INDEX === $pageName) {
            return [$prenom, $nom, $tel, $nomLycee, $lyceeLocalite, $lyceeAcademie, $rne, $equipes];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$prenom, $nom, $tel, $nomLycee, $lyceeLocalite, $lyceeAcademie, $rne, $equipes];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$prenom, $nom, $nomLycee, $lyceeLocalite, $lyceeAcademie, $rne, $equipes];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$prenom, $nom, $nomLycee, $lyceeLocalite, $lyceeAcademie, $rne, $equipes];
        }
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(CustomEditionFilter::new('edition'));

    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $session=$this->requestStack->getSession();
        $context = $this->adminContextProvider->getContext();
        $repositoryEdition = $this->getDoctrine()->getManager()->getRepository('App:Edition');

        if ($context->getRequest()->query->get('filters') == null) {
            $edition=$session->get('edition');

        } else {
            if (isset($context->getRequest()->query->get('filters')['edition'])) {

                $idEdition = $context->getRequest()->query->get('filters')['edition'];
                $edition = $repositoryEdition->findOneBy(['id' => $idEdition]);
               $session->set('titreedition', $edition);
            }


        }
        $qb = $this->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters)
            ->leftJoin('entity.equipes', 'eq')
            ->andWhere('eq.edition =:edition')
            ->setParameter('edition', $edition)
            ->leftJoin('entity.user', 'u')
            ->orderBy('u.nom', 'ASC');;
        $this->set_equipeString($edition, $qb);
        return $qb;
    }

    public function set_equipeString($edition, $qb)
    {//Equipesstring est un champ à contenu variable destiné à l'affichage des équipes d'un prof pour une session
        $em = $this->getDoctrine()->getManager();
        $repositoryEquipes = $this->getDoctrine()->getRepository('App:Equipesadmin');
        $listProfs = $qb->getQuery()->getResult();
        if ($listProfs != null) {
            foreach ($listProfs as $prof) {
                $equipestring = '';
                $equipes = $repositoryEquipes->createQueryBuilder('e')
                    ->where('e.edition =:edition')
                    ->setParameter('edition', $edition)
                    ->andWhere('e.idProf1 =:user OR e.idProf2 =:user')
                    ->setParameter('user', $prof->getUser())
                    ->getQuery()->getResult();

                if ($equipes != null) {
                    foreach ($equipes as $equipe) {
                        if ($equipe->getIdProf1() == $prof->getUser()) {
                            $encad = '(prof1)';
                        }
                        if ($equipe->getIdProf2() == $prof->getUser()) {
                            $encad = '(prof2)';
                        }
                        $equipestring = $equipestring . $equipe->getTitreProjet() . $encad;
                        if (next($equipes) != null) {
                            $equipestring = $equipestring . ' || ';
                        }
                    }
                    $prof->setEquipesstring($equipestring);
                    $em->persist($prof);
                    $em->flush();
                }
            }

        }


    }

    /**
     * @Route("/Professeurs/editer_tableau_excel,{idEdition},{selectionnes}", name="profs_tableau_excel")
     */

    public function editer_tableau_excel($idEdition,$selectionnes){


        $em = $this->getDoctrine()->getManager();
        $repositoryEdition = $this->getDoctrine()->getRepository('App:Edition');
        $repositoryEquipes = $this->getDoctrine()->getRepository('App:Equipesadmin');
        $edition=$repositoryEdition->findOneBy(['id'=>$idEdition]);
        $repositoryProfs = $this->getDoctrine()->getManager()->getRepository('App:Professeurs');

        $queryBuilder =  $repositoryProfs->createQueryBuilder('p')
            ->groupBy('p.user')
            ->leftJoin('p.equipes','eqs')
            ->andWhere('eqs.edition =:edition')
            ->setParameter('edition', $edition)
            ->leftJoin('p.user','u')
            ->orderBY('u.nom','ASC');
        if($selectionnes==true){
            $queryBuilder->andWhere('eqs.selectionnee = 1');

        }
        $listProfs= $queryBuilder->getQuery()->getResult();

        if($listProfs!=null){
            foreach($listProfs as $prof){
                $equipestring ='';

                $equipesQb=$repositoryEquipes->createQueryBuilder('e')
                    ->where('e.edition =:edition')
                    ->setParameter('edition',$edition)
                    ->andWhere('e.idProf1 =:user OR e.idProf2 =:user')
                    ->setParameter('user',$prof->getUser());
                if ($selectionnes==true){
                    $equipesQb->andWhere('e.selectionnee = 1');
                }

                $equipes=$equipesQb->getQuery()->getResult();

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
                    $equipestring=count($equipes).'~|~'.$equipestring;

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

            $equipesstring=explode('~|~',$prof->getEquipesstring());

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
