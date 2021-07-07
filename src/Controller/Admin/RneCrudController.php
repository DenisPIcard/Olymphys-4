<?php

namespace App\Controller\Admin;

use App\Entity\Rne;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class RneCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Rne::class;
    }


    public function configureFields(string $pageName): iterable
    {

            $nom=TextField::new('appellationOfficielle','Nom');
            $adresse =TextField::new('adresse','Adresse');
            $CP=TextField::new('codePostal','CP');
            $ville=TextField::new('commune','Ville');
            $academie =TextField::new('academie','Académie');
            $codeUAI= IntegerField::new('rne','Code UAI');
             if (Crud::PAGE_INDEX === $pageName) {
                 return [ $nom, $adresse, $CP,$ville, $academie, $codeUAI,];
             } elseif (Crud::PAGE_DETAIL === $pageName) {
                 return  [ $nom, $adresse, $CP, $ville, $academie, $codeUAI,];
             }
    }
    public function configureActions(Actions $actions): Actions
    {

        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_INDEX, Action::EDIT)
            ->remove(Crud::PAGE_INDEX, Action::DELETE);


    }

}
