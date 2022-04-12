<?php

namespace App\Controller\OdpfAdmin;

use App\Entity\Odpf\OdpfEquipesPassees;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class OdpfEquipesPasseesCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return OdpfEquipesPassees::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
