<?php

namespace App\Controller\Admin;

use App\Entity\Centrescia;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CentresciaCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Centrescia::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setSearchFields(['id', 'centre']);
    }

    public function configureFields(string $pageName): iterable
    {
        $centre = TextField::new('centre');
        $id = IntegerField::new('id', 'ID');
        $orga1 = AssociationField::new('orga1');
        $orga2 = AssociationField::new('orga2');
        $jurycia = AssociationField::new('jurycia');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$centre];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $centre, $orga1, $orga2, $jurycia];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$centre];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$centre];
        }
    }
}
