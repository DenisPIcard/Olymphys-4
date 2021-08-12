<?php

namespace App\Controller\Admin;

use App\Entity\Elevesinter;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Provider\AdminContextProvider;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class ElevesinterCrudController extends AbstractCrudController
{   private $session;
    private $adminContextProvider;
    public function __construct(SessionInterface $session,AdminContextProvider $adminContextProvider){
        $this->session=$session;
        $this->adminContextProvider=$adminContextProvider;

    }
    public static function getEntityFqcn(): string
    {
        return Elevesinter::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setSearchFields(['nom', 'prenom', 'courriel', 'equipe.id','equipe.edition','equipe.numero','equipe.titreProjet','equipe.lettre'])
            ->overrideTemplate('layout', 'bundles/EasyAdminBundle/list_eleves.html.twig');
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('equipe'))
            ->add(CustomEditionFilter :: new('edition'));


    }
    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->remove(Crud::PAGE_INDEX, Action::NEW);

    }
    public function configureFields(string $pageName): iterable
    {
        $nom = TextField::new('nom');
        $prenom = TextField::new('prenom');
        $genre = TextField::new('genre');
        $courriel = TextField::new('courriel');
        $equipe = AssociationField::new('equipe');
        $id = IntegerField::new('id', 'ID');
        $numsite = IntegerField::new('numsite');
        $classe = TextField::new('classe');
        $autorisationphotos = AssociationField::new('autorisationphotos');

        $equipeNumero = IntegerField::new('equipe.numero', ' Numéro équipe');
        $equipeTitreProjet = TextareaField::new('equipe.titreProjet','Projet');
        $equipeLyceeLocalite = TextareaField::new('equipe.lyceeLocalite', 'ville');
        $equipeEdition=TextareaField::new('equipe.edition','Edition');
        $autorisationphotosFichier = TextareaField::new('autorisationphotos.fichier','Autorisation photos');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$equipeEdition,$nom, $prenom, $genre, $courriel, $equipeNumero, $equipeTitreProjet, $equipeLyceeLocalite, $autorisationphotosFichier];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$equipeEdition, $nom, $prenom, $genre, $classe, $courriel, $equipe, $autorisationphotos];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$nom, $prenom, $genre, $courriel, $equipe];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$nom, $prenom, $genre,  $classe, $courriel, $equipe];
        }
    }
    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $context = $this->adminContextProvider->getContext();

        $repositoryEdition=$this->getDoctrine()->getManager()->getRepository('App:Edition');
        $repositoryEquipe=$this->getDoctrine()->getManager()->getRepository('App:Equipesadmin');
        if ($context->getRequest()->query->get('filters') == null) {

            $qb = $this->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters)
                ->leftJoin('entity.equipe','eq')
                ->andWhere('eq.edition =:edition')
                ->setParameter('edition', $this->session->get('edition'))
                ->orderBy('eq.numero','ASC');

        }
        else{
            if (isset($context->getRequest()->query->get('filters')['equipe'])){
                $idEquipe=$context->getRequest()->query->get('filters')['equipe']['value'];
                $equipe=$repositoryEquipe->findOneBy(['id'=>$idEquipe]);
                $this->session->set('titrepage',' Edition '.$equipe);}

            $qb = $this->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters);
        }
        if (isset($context->getRequest()->query->get('filters')['edition'])){
            $idEdition=$context->getRequest()->query->get('filters')['edition'];
            $edition=$repositoryEdition->findOneBy(['id'=>$idEdition]);
            if (!isset($context->getRequest()->query->get('filters')['equipe'])){
                $this->session->set('titrepage', $edition.'<sup>e</sup>'.' édition' );
            }


        $qb = $this->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters)
                    ->leftJoin('entity.equipe','eq')
                    ->andWhere('eq.edition =:edition')
                    ->setParameter('edition',$edition)
                    ->orderBy('eq.numero','ASC');

    }

        return $qb;
    }
    /**
     *@Route("/Admin/ElevesinteradminCrud/eleves_tableau_excel,{ideditionequipe}", name="eleves_tableau_excel")
     */
    public function elevestableauexcel($ideditionequipe){
        $idedition=explode('-',$ideditionequipe)[0];
        $idequipe=explode('-',$ideditionequipe)[1];


        $repositoryEleves = $this->getDoctrine()->getRepository('App:Elevesinter');
        $repositoryEdition = $this->getDoctrine()->getRepository('App:Edition');
        $repositoryEquipes = $this->getDoctrine()->getRepository('App:Equipesadmin');
        $edition=$repositoryEdition->findOneBy(['id'=>$idedition]);


        $queryBuilder = $repositoryEleves->createQueryBuilder('e')
            ->leftJoin('e.equipe','eq')
            ->andWhere('eq.edition =:edition')
            ->setParameter('edition',$edition)
            ->orderBy('eq.numero','ASC');
        if ($idequipe!=0){
            $equipe=$repositoryEquipes->finOneBy(['id'=>$idequipe]);
            $queryBuilder
                ->andWhere('equipe =:equipe')
                ->setParameter('equipe',$equipe);
        }
        $liste_eleves = $queryBuilder->getQuery()->getResult();


        //dd($edition);
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator("Olymphys")
            ->setLastModifiedBy("Olymphys")
            ->setTitle("CN  ".$edition->getEd()."e édition -Tableau destiné au comité")
            ->setSubject("Tableau destiné au comité")
            ->setDescription("Office 2007 XLSX liste des éleves")
            ->setKeywords("Office 2007 XLSX")
            ->setCategory("Test result file");

        $sheet = $spreadsheet->getActiveSheet();
        foreach(['A','B','C','D','E','F','G','H','I','J']as $letter) {
            $sheet->getColumnDimension($letter)->setAutoSize(true);
        }

        $ligne=1;

        $sheet
            ->setCellValue('A'.$ligne, 'Edition')
            ->setCellValue('B'.$ligne, 'Numero equipe')
            ->setCellValue('C'.$ligne, 'Lettre equipe')
            ->setCellValue('D'.$ligne, 'Prenom')
            ->setCellValue('E'.$ligne, 'Nom')
            ->setCellValue('F'.$ligne, 'Courriel')
            ->setCellValue('G'.$ligne, 'Equipe')
            ->setCellValue('H'.$ligne, 'Nom du lycée')
            ->setCellValue('I'.$ligne, 'Commune')
            ->setCellValue('J'.$ligne, 'Académie');


        ;

        $ligne +=1;

        foreach ($liste_eleves as $eleve) {
            $rne = $eleve->getEquipe()->getRneId();

            $sheet->setCellValue('A' . $ligne, $eleve->getEquipe()->getEdition())
                ->setCellValue('B' . $ligne, $eleve->getEquipe()->getNumero());
                if ($eleve->getEquipe()->getLettre() != null) {
                  $sheet->setCellValue('C' . $ligne, $eleve->getEquipe()->getLettre());
                 }
                $sheet->setCellValue('D'.$ligne, $eleve->getPrenom())
                ->setCellValue('E'.$ligne, $eleve->getNom())
                ->setCellValue('F'.$ligne, $eleve->getCourriel())
                ->setCellValue('G'.$ligne, $eleve->getEquipe())
                ->setCellValue('H'.$ligne, $rne->getNom())
                ->setCellValue('I'.$ligne, $rne->getCommune())
                ->setCellValue('J'.$ligne, $rne->getAcademie());

            $ligne +=1;
        }

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="equipes.xls"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xls($spreadsheet);
        //$writer= PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        //$writer =  \PhpOffice\PhpSpreadsheet\Writer\Xls($spreadsheet);
        // $writer =IOFactory::createWriter($spreadsheet, 'Xlsx');
        ob_end_clean();
        $writer->save('php://output');

    }
}
