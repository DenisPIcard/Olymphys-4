<?php

namespace App\Controller\OdpfAdmin;

use App\Entity\Odpf\OdpfEditionsPassees;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class OdpfEditionsPasseesCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return OdpfEditionsPassees::class;
    }

/*
    public function configureFields(string $pageName): iterable
    {
        return [



            ]
    }
*/
}
