<?php

namespace App\Controller\Admin;

use App\Entity\Fichiersequipes;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;

class FichiersequipesresumesCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Fichiersequipes::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, '<h2>Les résumés</h2>')
            ->setPageTitle(Crud::PAGE_EDIT, 'Les fichiers sont automatiquement renommés')
            ->setPageTitle(Crud::PAGE_NEW, 'Les fichiers sont automatiquement renommés')
            ->setSearchFields(['id', 'fichier', 'typefichier', 'nomautorisation'])
            ->setPaginatorPageSize(50)
            ->overrideTemplate('crud/index', 'Admin/customizations/list_resumes.html.twig');
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('edition'));
    }

    public function configureFields(string $pageName): iterable
    {
        $panel1 = FormField::addPanel('<font color="red" > Déposer un nouvau résumé </font> ');
        $equipe = AssociationField::new('equipe');
        $fichierFile = Field::new('fichierFile');
        $panel2 = FormField::addPanel('<font color="red" > Modifier le résumé </font> ');
        $id = IntegerField::new('id', 'ID');
        $fichier = TextField::new('fichier')->setTemplatePath('Admin\\customizations\\vich_uploader_memoiresinter.html.twig');
        $typefichier = IntegerField::new('typefichier');
        $national = Field::new('national');
        $updatedAt = DateTimeField::new('updatedAt');
        $nomautorisation = TextField::new('nomautorisation');
        $edition = AssociationField::new('edition');
        $eleve = AssociationField::new('eleve');
        $prof = AssociationField::new('prof');
        $editionEd = TextareaField::new('edition.ed');
        $equipeNumero = TextareaField::new('equipe.numero');
        $equipeLettre = TextareaField::new('equipe.lettre');
        $equipeCentreCentre = TextareaField::new('equipe.centre.centre');
        $equipeTitreprojet = TextareaField::new('equipe.titreprojet');
        $updatedat = DateTimeField::new('updatedat', 'Déposé le ');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$editionEd, $equipeNumero, $equipeLettre, $equipeCentreCentre, $equipeTitreprojet, $fichier, $updatedat];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $fichier, $typefichier, $national, $updatedAt, $nomautorisation, $edition, $equipe, $eleve, $prof];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$panel1, $equipe, $fichierFile];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$panel2, $equipe, $fichierFile];
        }
    }
}
