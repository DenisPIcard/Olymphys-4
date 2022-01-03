<?php

namespace App\Controller\Admin;

use App\Entity\Visites;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class VisitesCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Visites::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_EDIT, 'Modifier une visite')
            ->setSearchFields(['id', 'intitule']);
    }

    public function configureFields(string $pageName): iterable
    {
        $intitule = TextField::new('intitule');
        $attribue = Field::new('attribue');
        $id = IntegerField::new('id', 'ID');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$intitule, $attribue];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $intitule, $attribue];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$intitule, $attribue];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$intitule, $attribue];
        }
    }
    public function configureActions(Actions $actions): Actions
        {
        $actions = $actions
        ->add(Crud::PAGE_EDIT, Action::INDEX, 'Retour à la liste')
        ->add(Crud::PAGE_NEW, Action::INDEX, 'Retour à la liste')
        ->remove(Crud::PAGE_NEW, Action::SAVE_AND_ADD_ANOTHER);




        return $actions;
        }
}
