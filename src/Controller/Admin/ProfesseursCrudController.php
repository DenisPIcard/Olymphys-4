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
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\String\UnicodeString;

class ProfesseursCrudController extends AbstractCrudController
{

    private $session;
    private $adminContextProvider;

    public function __construct(SessionInterface $session, AdminContextProvider $adminContextProvider)
    {
        $this->session = $session;
        $this->adminContextProvider=$adminContextProvider;

    }

    public static function getEntityFqcn(): string
    {
        return Professeurs::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        $exp = new UnicodeString('<sup>e</sup>');

        $editioned = $this->session->get('edition')->getEd();

        return $crud
            //->setPageTitle('index', 'Liste des équipe de la '.$editioned.$exp.' édition')
            ->setPageTitle(Crud::PAGE_DETAIL, 'Liste des professeurs')
            ->setSearchFields(['id', 'lettre', 'numero', 'titreProjet', 'nomLycee', 'denominationLycee', 'lyceeLocalite', 'lyceeAcademie', 'prenomProf1', 'nomProf1', 'prenomProf2', 'nomProf2', 'rne', 'contribfinance', 'origineprojet', 'recompense', 'partenaire', 'description'])
            ->setPaginatorPageSize(50);
            //->overrideTemplates(['layout' => 'bundles/EasyAdminBundle/list_profs.html.twig',]);


    }

    public function configureActions(Actions $actions): Actions
    {
        $tableauexcel = Action::new('professeursstableauexcel', 'Créer un tableau excel des professeurs')
            // if the route needs parameters, you can define them:
            // 1) using an array
            ->linkToRoute('eleves_tableau_excel', ['ideditioncentre' => '3-0']);
        return $actions
            ->add(Crud::PAGE_DETAIL, Action::DETAIL)
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_INDEX, Action::EDIT);

    }

    public function configureFields(string $pageName): iterable
    {
        $nom = IntegerField::new('user.nom', 'N°');
        $prenom = TextField::new('user.prenom');
        $nomLycee = TextField::new('user.rneId.appellationOfficielle', 'Lycée');
        $lyceeLocalite = TextField::new('user.rneId.commune', 'Ville');
        $lyceeAcademie = TextField::new('user.rneId.academie', 'Académie');
        $rne = TextField::new('user.rne', 'Code UAI');
        $equipes = IntegerField::new('equipesstring', 'Equipes');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$prenom, $nom, $nomLycee, $lyceeLocalite, $lyceeAcademie, $rne, $equipes];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$prenom, $nom, $nomLycee, $lyceeLocalite, $lyceeAcademie, $rne, $equipes];
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
        $context = $this->adminContextProvider->getContext();
        $repositoryEdition=$this->getDoctrine()->getManager()->getRepository('App:Edition');

        if ($context->getRequest()->query->get('filters') == null) {
            $this->set_equipeString($this->session->get('edition'));
            $qb = $this->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters)
                ->leftJoin('entity.equipes','eq')
                ->andWhere('eq.edition =:edition')
                ->setParameter('edition', $this->session->get('edition'));

        }
        else{
            if (isset($context->getRequest()->query->get('filters')['edition'])){

                $idEdition=$context->getRequest()->query->get('filters')['edition'];
                $edition=$repositoryEdition->findOneBy(['id'=>$idEdition]);
                $this->session->set('titreedition',$edition);}
                $this->set_equipeString($edition);
                $qb = $this->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters)
                    ->leftJoin('entity.equipes','eq')
                    ->andWhere('eq.edition =:edition')
                    ->setParameter('edition', $edition);
        }

        return $qb;
    }
    public function set_equipeString($edition){
        $repositoryEquipesadmin=$this->getDoctrine()->getManager()->getRepository('App:Equipesadmin');
        $listeEquipes= $repositoryEquipesadmin->findBy(['edition'=>$edition]);
        foreach ($listeEquipes as $equipe){
            $Idprof1=$equipe->



        }

    }


}
