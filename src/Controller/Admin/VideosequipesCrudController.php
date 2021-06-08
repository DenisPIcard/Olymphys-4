<?php

namespace App\Controller\Admin;

use App\Entity\Videosequipes;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;

class VideosequipesCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Videosequipes::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Videosequipes')
            ->setEntityLabelInPlural('Videosequipes')
            ->setPageTitle(Crud::PAGE_INDEX, '<font color="yellow"><h2>Les vidéos des équipes</h2></font>')
            ->setPageTitle(Crud::PAGE_EDIT, 'Donner un nom à la vidéo')
            ->setPageTitle(Crud::PAGE_NEW, 'Les fichiers sont automatiquement renommés')
            ->setSearchFields(['id', 'lien', 'nom'])
            ->setPaginatorPageSize(50)
            ->overrideTemplate('crud/index', 'Admin/customizations/list_videos.html.twig');
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('edition'))
            ->add(EntityFilter::new('equipe'));
    }

    public function configureFields(string $pageName): iterable
    {
        $panel1 = FormField::addPanel('<font color="red" > Choisir le fichier à déposer </font> ');
        $equipe = AssociationField::new('equipe');
        $lien = UrlField::new('lien');
        $nom = TextareaField::new('nom');
        $panel2 = FormField::addPanel('<font color="red" > Choisir l\'équipe </font> ');
        $id = IntegerField::new('id', 'ID');
        $updatedAt = DateTimeField::new('updatedAt');
        $edition = AssociationField::new('edition');
        $equipeNumero = TextareaField::new('equipe.numero');
        $equipeLettre = TextareaField::new('equipe.lettre');
        $equipeCentreCentre = TextareaField::new('equipe.centre.centre');
        $equipeTitreprojet = TextareaField::new('equipe.titreprojet');
        $updatedat = DateTimeField::new('updatedat', 'Déposé le ');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$equipeNumero, $equipeLettre, $equipeCentreCentre, $equipeTitreprojet, $lien, $nom, $updatedat];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $lien, $nom, $updatedAt, $edition, $equipe];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$panel1, $equipe, $lien, $nom];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$panel2, $equipe, $lien, $nom];
        }
    }
}
