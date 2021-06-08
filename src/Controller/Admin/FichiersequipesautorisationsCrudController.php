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

class FichiersequipesautorisationsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Fichiersequipes::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, '<h2>Les autorisations photos</h2>')
            ->setPageTitle(Crud::PAGE_NEW, 'Les fichiers sont automatiquement renommés')
            ->setSearchFields(['id', 'fichier', 'typefichier', 'nomautorisation'])
            ->setPaginatorPageSize(50)
            ->overrideTemplate('crud/index', 'Admin/customizations/list_autorisations.html.twig');
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('eleve'));
    }

    public function configureFields(string $pageName): iterable
    {
        $panel1 = FormField::addPanel('<font color="red" > Déposer une nouvelle autorisation photo </font> ');
        $eleve = AssociationField::new('eleve');
        $prof = AssociationField::new('prof');
        $fichierFile = Field::new('fichierFile');
        $fichier = TextField::new('fichier')->setTemplatePath('Admin\\customizations\\vich_uploader_memoiresinter.html.twig');
        $typefichier = IntegerField::new('typefichier');
        $national = Field::new('national');
        $updatedAt = DateTimeField::new('updatedAt');
        $nomautorisation = TextField::new('nomautorisation');
        $edition = AssociationField::new('edition');
        $equipe = AssociationField::new('equipe');
        $id = IntegerField::new('id', 'ID');
        $equipeNumero = TextareaField::new('equipe.numero');
        $equipeLettre = TextareaField::new('equipe.lettre');
        $equipeTitreprojet = TextareaField::new('equipe.titreprojet');
        $updatedat = DateTimeField::new('updatedat', 'Déposé le ');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$equipeNumero, $equipeLettre, $equipeTitreprojet, $fichier, $updatedat];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $fichier, $typefichier, $national, $updatedAt, $nomautorisation, $edition, $equipe, $eleve, $prof];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$panel1, $eleve, $prof, $fichierFile];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$fichier, $typefichier, $national, $updatedAt, $nomautorisation, $edition, $equipe, $eleve, $prof];
        }
    }
}
