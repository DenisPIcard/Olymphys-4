<?php

namespace App\Controller\Admin;

use App\Entity\Repartprix;
use App\Entity\Coefficients;
use App\Form\CoefficientsType;
use App\Repository\CoefficientsRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class CoefficientsCrudController extends AbstractCrudController
{   public static function getEntityFqcn(): string
        {
            return Coefficients::class;
        }
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_EDIT, 'modifier les coefficients');

    }

}
