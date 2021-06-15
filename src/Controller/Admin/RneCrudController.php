<?php

namespace App\Controller\Admin;

use App\Entity\Rne;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class RneCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Rne::class;
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
