<?php

namespace App\Controller\Admin;

use App\Entity\Equipesadmin;
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
    {
      return $crud
            ->setPageTitle(Crud::PAGE_EDIT, 'modifier une équipe')
            ->setPageTitle(Crud::PAGE_NEW, 'Ajouter une équipe')
            ->setSearchFields(['id', 'lettre', 'numero', 'titreProjet', 'nomLycee', 'denominationLycee', 'lyceeLocalite', 'lyceeAcademie', 'prenomProf1', 'nomProf1', 'prenomProf2', 'nomProf2', 'rne', 'contribfinance', 'origineprojet', 'recompense', 'partenaire', 'description'])
            ->setPaginatorPageSize(50);



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
        $titreProjet = TextField::new('titreProjet');
        $centre = AssociationField::new('centre');
        $idProf1 = AssociationField::new('idProf1');
        $nomProf1 = TextField::new('nomProf1');
        $prenomProf1 = TextField::new('prenomProf1');
        $idProf2 = AssociationField::new('idProf2');
        $nomProf2 = TextField::new('nomProf2');
        $prenomProf2 = TextField::new('prenomProf2');
        $selectionnee = Field::new('selectionnee');
        $id = IntegerField::new('id', 'ID');
        $nomLycee = TextField::new('nomLycee');
        $denominationLycee = TextField::new('denominationLycee');
        $lyceeLocalite = TextField::new('lyceeLocalite');
        $lyceeAcademie = TextField::new('lyceeAcademie');
        $rne = TextField::new('rne');
        $contribfinance = TextField::new('contribfinance');
        $origineprojet = TextField::new('origineprojet');
        $recompense = TextField::new('recompense');
        $partenaire = TextField::new('partenaire');
        $createdAt = DateTimeField::new('createdAt');
        $description = TextareaField::new('description');
        $inscrite = Field::new('inscrite');
        $rneId = AssociationField::new('rneId');
        $edition = AssociationField::new('edition');
        $editionEd = TextareaField::new('edition.ed');
        $centreCentre = TextareaField::new('centre.centre');
        $lycee = TextareaField::new('Lycee');
        $prof1 = TextareaField::new('Prof1');
        $prof2 = TextareaField::new('Prof2');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$editionEd, $centreCentre, $numero, $lettre, $titreProjet, $lyceeAcademie, $lycee, $selectionnee, $prof1, $prof2, $inscrite];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $lettre, $numero, $selectionnee, $titreProjet, $nomLycee, $denominationLycee, $lyceeLocalite, $lyceeAcademie, $prenomProf1, $nomProf1, $prenomProf2, $nomProf2, $rne, $contribfinance, $origineprojet, $recompense, $partenaire, $createdAt, $description, $inscrite, $rneId, $centre, $edition, $idProf1, $idProf2];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$numero, $lettre, $titreProjet, $centre, $idProf1, $nomProf1, $prenomProf1, $idProf2, $nomProf2, $prenomProf2];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$numero, $lettre, $titreProjet, $centre, $selectionnee, $idProf1, $nomProf1, $prenomProf1, $idProf2, $nomProf2, $prenomProf2];
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
        // ...



}
