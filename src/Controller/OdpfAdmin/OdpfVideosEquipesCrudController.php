<?php

namespace App\Controller\OdpfAdmin;

use App\Entity\Odpf\OdpfEquipesPassees;
use App\Entity\Odpf\OdpfFichierspasses;
use App\Entity\Odpf\OdpfVideosequipes;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class OdpfVideosEquipesCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return OdpfVideosequipes::class;
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