<?php

namespace App\Controller\Admin;

use App\Entity\Elevesinter;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;

class ElevesinterCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Elevesinter::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setSearchFields(['id', 'numsite', 'nom', 'prenom', 'genre', 'classe', 'courriel'])
            ->overrideTemplate('crud/index', 'Admin/customizations/list_eleves.html.twig');
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('equipe'));
    }

    public function configureFields(string $pageName): iterable
    {
        $nom = TextField::new('nom');
        $prenom = TextField::new('prenom');
        $genre = TextField::new('genre');
        $courriel = TextField::new('courriel');
        $equipe = AssociationField::new('equipe');
        $id = IntegerField::new('id', 'ID');
        $numsite = IntegerField::new('numsite');
        $classe = TextField::new('classe');
        $autorisationphotos = AssociationField::new('autorisationphotos');
        $prenom = TextareaField::new('prenom ');
        $equipeNumero = TextareaField::new('equipe.numero', ' Numéro équipe');
        $equipeTitreProjet = TextareaField::new('equipe.titreProjet');
        $equipeLyceeLocalite = TextareaField::new('equipe.lyceeLocalite', 'ville');
        $autorisationphotosFichier = TextareaField::new('autorisationphotos.fichier');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$nom, $prenom, $genre, $courriel, $equipeNumero, $equipeTitreProjet, $equipeLyceeLocalite, $autorisationphotosFichier];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $numsite, $nom, $prenom, $genre, $classe, $courriel, $equipe, $autorisationphotos];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$nom, $prenom, $genre, $courriel, $equipe];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$nom, $prenom, $genre, $courriel, $equipe];
        }
    }
}
