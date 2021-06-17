<?php

namespace App\Controller\Admin;

use App\Entity\Equipesadmin;
use App\Entity\Edition;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use PhpOffice\PhpSpreadsheet\Calculation\DateTime;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\String\UnicodeString;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Provider\AdminContextProvider;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use Symfony\Component\Routing\Annotation\Route;

class EquipesadminCrudController extends AbstractCrudController

{   private $session;
    private $adminContextProvider;
    public function __construct(SessionInterface $session,AdminContextProvider $adminContextProvider){
    $this->session=$session;
    $this->adminContextProvider=$adminContextProvider;

}
    public static function getEntityFqcn(): string
    {
        return Equipesadmin::class;
    }

    public function configureCrud(Crud $crud): Crud
    {  $exp =  new UnicodeString('<sup>e</sup>');

      return $crud
            ->setPageTitle('index', 'Liste des équipe de la '.$this->session->get('edition')->getEd().$exp.' édition')
            ->setPageTitle(Crud::PAGE_EDIT, 'modifier une équipe')
            ->setPageTitle(Crud::PAGE_NEW, 'Ajouter une équipe')
            ->setSearchFields(['id', 'lettre', 'numero', 'titreProjet', 'nomLycee', 'denominationLycee', 'lyceeLocalite', 'lyceeAcademie', 'prenomProf1', 'nomProf1', 'prenomProf2', 'nomProf2', 'rne', 'contribfinance', 'origineprojet', 'recompense', 'partenaire', 'description'])
            ->setPaginatorPageSize(50)
            ->overrideTemplate('layout', 'Admin/customizations/list_equipescia.html.twig');



    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('edition'))
            ->add(EntityFilter::new('centre'));
    }

    public function configureFields(string $pageName): iterable
    {
        $numero = IntegerField::new('numero');
        $lettre = TextField::new('lettre');
        $titreProjet = TextField::new('titreProjet','Projet');
        $centre = AssociationField::new('centre');
        $idProf1 = AssociationField::new('idProf1','Prof1');
        $nomProf1 = TextField::new('nomProf1');
        $prenomProf1 = TextField::new('prenomProf1');
        $idProf2 = AssociationField::new('idProf2','Prof2');
        $nomProf2 = TextField::new('nomProf2');
        $prenomProf2 = TextField::new('prenomProf2');
        $selectionnee = Field::new('selectionnee');
        $id = IntegerField::new('id', 'ID');
        $nomLycee = TextField::new('nomLycee','Lycée');
        $denominationLycee = TextField::new('denominationLycee');
        $lyceeLocalite = TextField::new('lyceeLocalite','Ville');
        $lyceeAcademie = TextField::new('lyceeAcademie','Académie');
        $rne = TextField::new('rne','Code UAI');
        $contribfinance = TextField::new('contribfinance');
        $origineprojet = TextField::new('origineprojet');
        //$recompense = TextField::new('recompense');
        $partenaire = TextField::new('partenaire');
        $createdAt = DateField::new('createdAt','Date d\'inscription');
        $description = TextareaField::new('description');
        $inscrite = Field::new('inscrite');
        $rneId = AssociationField::new('rneId');
        $edition = AssociationField::new('edition','Edition');
        $editionEd = TextareaField::new('edition.ed','Edition');
        $centreCentre = TextareaField::new('centre.centre','Centre CIA');
        $lycee = TextareaField::new('Lycee');
        $prof1 = TextareaField::new('Prof1');
        $prof2 = TextareaField::new('Prof2');
        $nbeleves = IntegerField::new('nbeleves','Nbre d\'élèves');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$editionEd, $centreCentre, $numero, $lettre, $titreProjet, $lyceeAcademie, $lycee, $selectionnee, $prof1, $prof2, $nbeleves,$inscrite,$origineprojet,$createdAt];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $lettre, $numero, $selectionnee, $titreProjet, $nomLycee, $denominationLycee, $lyceeLocalite, $lyceeAcademie, $prenomProf1, $nomProf1, $prenomProf2, $nomProf2, $rne, $contribfinance, $origineprojet,  $partenaire, $createdAt, $description, $inscrite, $rneId, $centre, $edition, $idProf1, $idProf2];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$numero, $lettre, $titreProjet, $centre, $idProf1, $nomProf1, $prenomProf1, $idProf2, $nomProf2, $prenomProf2];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$numero, $lettre, $titreProjet, $centre, $selectionnee, $idProf1,$idProf2,$inscrite,$description,$contribfinance,$partenaire ];
        }
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $context = $this->adminContextProvider->getContext();

        if ($context->getRequest()->query->get('filters') == null) {

            $qb = $this->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters)
                ->andWhere('entity.edition =:edition')
                ->setParameter('edition', $this->session->get('edition'));
        }
        else{
            $qb = $this->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters);
            }

        return $qb;
    }
  /**
   *@Route("/Admin/EquipesadminCrud/equipes_tableau_excel,{ideditioncentre}", name="equipes_tableau_excel")
   */
  public function equipestableauexcel($ideditioncentre){
      $idedition=explode('-',$ideditioncentre)[0];
      $idcentre=explode('-',$ideditioncentre)[1];


      $repositoryEleve = $this->getDoctrine()->getRepository('App:Elevesinter');
      $repositoryCentre = $this->getDoctrine()->getRepository('App:Centrescia');
      $repositoryProf = $this->getDoctrine()->getRepository('App:User');
      $repositoryEdition = $this->getDoctrine()->getRepository('App:Edition');
      $repositoryEquipes = $this->getDoctrine()->getRepository('App:Equipesadmin');
      $edition=$repositoryEdition->findOneBy(['id'=>$idedition]);


      $queryBuilder = $repositoryEquipes->createQueryBuilder('e')
          ->andWhere('e.edition =:edition')
          ->andWhere('e.inscrite = TRUE')
          ->setParameter('edition',$edition)
          ->andWhere('e.numero < 100')
          ->orderBy('e.numero','ASC');
      if ($idcentre!=0){
          $centre=$repositoryCentre->finOneBy(['id'=>$idcentre]);
          $queryBuilder
              ->andWhere('centre =:centre')
              ->setParameter('centre',$centre);
      }
      $liste_equipes = $queryBuilder->getQuery()->getResult();


      //dd($edition);
      $spreadsheet = new Spreadsheet();
      $spreadsheet->getProperties()
          ->setCreator("Olymphys")
          ->setLastModifiedBy("Olymphys")
          ->setTitle("CN  ".$edition->getEd()."e édition -Tableau destiné au comité")
          ->setSubject("Tableau destiné au comité")
          ->setDescription("Office 2007 XLSX liste des équipes")
          ->setKeywords("Office 2007 XLSX")
          ->setCategory("Test result file");

      $sheet = $spreadsheet->getActiveSheet();
      foreach(['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','R','S','T']as $letter) {
          $sheet->getColumnDimension($letter)->setAutoSize(true);
      }

      $ligne=1;

      $sheet
          ->setCellValue('A'.$ligne, 'Idequipe')
          ->setCellValue('B'.$ligne, 'Date de création')
          ->setCellValue('C'.$ligne, 'nom équipe')
          ->setCellValue('D'.$ligne, 'Numéro')
          ->setCellValue('E'.$ligne, 'Lettre')
          ->setCellValue('F'.$ligne, 'inscrite')
          ->setCellValue('G'.$ligne, 'sélectionnée')
          ->setCellValue('H'.$ligne, 'Nom du lycée')
          ->setCellValue('I'.$ligne, 'Commune')
          ->setCellValue('J'.$ligne, 'Académie')
          ->setCellValue('K'.$ligne, 'rne')
          ->setCellValue('L'.$ligne, 'Description')
          ->setCellValue('M'.$ligne, 'Origine du projet')
          ->setCellValue('N'.$ligne, 'Contribution financière à ')
          ->setCellValue('O'.$ligne, 'Année')
          ->setCellValue('P'.$ligne, 'Prof 1')
          ->setCellValue('Q'.$ligne, 'mail prof1')
          ->setCellValue('R'.$ligne, 'Prof2')
          ->setCellValue('S'.$ligne, 'mail prof2')
          ->setCellValue('T'.$ligne, 'Nombre d\'élèves')

      ;

      $ligne +=1;

      foreach ($liste_equipes as $equipe)
      {   $nbEleves=count($repositoryEleve->findByEquipe(['equipe'=>$equipe]));
          $idprof1=$equipe->getIdProf1();
          $idprof2=$equipe->getIdProf2();
          $prof1=$repositoryProf->findOneById(['id'=>$idprof1]);
          $prof2=$repositoryProf->findOneById(['id'=>$idprof2]);
          $rne=$equipe->getRneId();

          $sheet->setCellValue('A'.$ligne,$equipe->getId() )
              ->setCellValue('B'.$ligne, $equipe->getCreatedAt())
              ->setCellValue('C'.$ligne, $equipe->getTitreprojet())
              ->setCellValue('D'.$ligne, $equipe->getNumero())
              ->setCellValue('E'.$ligne, $equipe->getLettre())
              ->setCellValue('F'.$ligne, $equipe->getInscrite())
              ->setCellValue('G'.$ligne, $equipe->getSelectionnee())
              ->setCellValue('H'.$ligne, $rne->getNom())
              ->setCellValue('I'.$ligne, $rne->getCommune())
              ->setCellValue('J'.$ligne, $rne->getAcademie())
              ->setCellValue('K'.$ligne, $rne->getRne())
              ->setCellValue('L'.$ligne, $equipe->getDescription())
              ->setCellValue('M'.$ligne, $equipe->getOrigineprojet())
              ->setCellValue('N'.$ligne, $equipe->getContribfinance())
              ->setCellValue('O'.$ligne, $edition->getAnnee())
              ->setCellValue('P'.$ligne, $prof1->getNomPrenom())
              ->setCellValue('Q'.$ligne,$prof1->getEmail());
          if($prof2!=null){
              $sheet->setCellValue('R'.$ligne, $prof2->getNomPrenom())
                  ->setCellValue('S'.$ligne,$prof2->getEmail());
          }
          $sheet->setCellValue('T'.$ligne, $nbEleves);
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
