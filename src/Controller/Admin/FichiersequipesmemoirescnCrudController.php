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

class FichiersequipesmemoirescnCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Fichiersequipes::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, '<font color="red"><h2>Concours national : Memoires </h2></font>')
            ->setPageTitle(Crud::PAGE_EDIT, 'Les fichiers sont automatiquement renommés selon leur catégorie : memoire ou annexe')
            ->setPageTitle(Crud::PAGE_NEW, 'Les fichiers sont automatiquement renommés selon leur catégorie : memoire ou annexe')
            ->setSearchFields(['id', 'fichier', 'typefichier', 'nomautorisation'])
            ->setPaginatorPageSize(30)
            ->overrideTemplate('crud/index', 'Admin/customizations/list_memoirescn.html.twig');
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('edition'));
    }

    public function configureFields(string $pageName): iterable
    {
        $panel1 = FormField::addPanel('<font color="red" > Choisir le fichier à déposer </font> ');
        $edition = AssociationField::new('edition');
        $equipe = AssociationField::new('equipe');
        $fichierFile = Field::new('fichierFile');
        $panel2 = FormField::addPanel('<font color="red" > Choisir le type de fichier </font>');
        $typefichier = IntegerField::new('typefichier');
        $panel3 = FormField::addPanel('<font color="red" > Choisir l\'équipe </font> ');
        $panel4 = FormField::addPanel('<font color="red" > Choisir le type de fichier </font>');
        $id = IntegerField::new('id', 'ID');
        $fichier = TextField::new('fichier')->setTemplatePath('Admin\\customizations\\vich_uploader_memoirescn.html.twig');
        $national = Field::new('national');
        $updatedAt = DateTimeField::new('updatedAt');
        $nomautorisation = TextField::new('nomautorisation');
        $eleve = AssociationField::new('eleve');
        $prof = AssociationField::new('prof');
        $equipeLettre = TextareaField::new('equipe.lettre');
        $equipeTitreProjet = TextareaField::new('equipe.titreProjet');
        $updatedat = DateTimeField::new('updatedat', 'Déposé le ');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$equipeLettre, $equipeTitreProjet, $fichier, $updatedat];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $fichier, $typefichier, $national, $updatedAt, $nomautorisation, $edition, $equipe, $eleve, $prof];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$panel1, $edition, $equipe, $fichierFile, $panel2, $typefichier];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$panel3, $equipe, $panel4, $typefichier, $fichierFile];
        }
    }
}
