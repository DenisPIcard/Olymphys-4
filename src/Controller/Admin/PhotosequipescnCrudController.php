<?php

namespace App\Controller\Admin;

use App\Entity\Photos;
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

class PhotosequipescnCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Photos::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, '<font color="green"><h2>Les photos des équipes</h2></font>')
            ->setPageTitle(Crud::PAGE_EDIT, 'Les fichiers sont automatiquement renommés')
            ->setPageTitle(Crud::PAGE_NEW, 'Les fichiers sont automatiquement renommés.')
            ->setSearchFields(['id', 'photo', 'coment'])
            ->setPaginatorPageSize(30)
            ->overrideTemplate('crud/index', 'Admin/customizations/list_photos_cn.html.twig');
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
        $edition = AssociationField::new('edition');
        $equipe = AssociationField::new('equipe');
        $photoFile = Field::new('photoFile');
        $panel2 = FormField::addPanel('<font color="red" > Choisir l\'équipe </font> ');
        $id = IntegerField::new('id', 'ID');
        $photo = TextField::new('photo')->setTemplatePath('Admin\\customizations\\vich_uploader_image_equipesinter.html.twig');
        $coment = TextField::new('coment');
        $national = Field::new('national');
        $updatedAt = DateTimeField::new('updatedAt', 'Déposé le ');
        $equipeLettre = TextareaField::new('equipe.lettre');
        $equipeTitreprojet = TextareaField::new('equipe.titreprojet');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$equipeLettre, $equipeTitreprojet, $photo, $updatedAt];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $photo, $coment, $national, $updatedAt, $equipe, $edition];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$panel1, $edition, $equipe, $photoFile];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$panel2, $equipe, $edition, $photoFile];
        }
    }
}
