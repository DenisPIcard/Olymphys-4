<?php

namespace App\Controller\Admin;

use App\Entity\Equipes;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class SelectionneesCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Equipes::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setSearchFields(['id', 'lettre', 'titreProjet', 'ordre', 'heure', 'salle', 'total', 'classement', 'rang', 'nbNotes', 'sallesecours', 'code'])
            ->setPaginatorPageSize(25);
    }

    public function configureFields(string $pageName): iterable
    {
        $lettre = TextField::new('equipeinter.lettre', 'lettre');
        $titreProjet = TextField::new('equipeinter.titreProjet', 'projet');
        $ordre = IntegerField::new('ordre');
        $heure = TextField::new('heure');
        $salle = TextField::new('salle');
        $total = IntegerField::new('total');
        $classement = TextField::new('classement');
        $rang = IntegerField::new('rang');
        $nbNotes = IntegerField::new('nbNotes');
        $visite = AssociationField::new('visite');
        $cadeau = AssociationField::new('cadeau');
        $phrases = AssociationField::new('phrases');
        $liaison = AssociationField::new('liaison');
        $prix = AssociationField::new('prix');
        $infoequipe = AssociationField::new('infoequipe');
        $eleves = AssociationField::new('eleves');
        $notess = AssociationField::new('notess');
        $observateur = AssociationField::new('observateur');
        $Academie = TextareaField::new('equipeinter.lyceeAcademie', 'académie');
        $Lycee = TextareaField::new('equipeinter.Lycee', 'lycée');
        $infoequipeTitreProjet = TextareaField::new('equipeinter.TitreProjet');
        $id = IntegerField::new('id', 'ID');

        //dd($_REQUEST);
        if (Crud::PAGE_INDEX === $pageName) {

            if (isset($_REQUEST['palmares'])){
                if ($_REQUEST['palmares'] == false) {
                    return [$lettre, $Academie, $Lycee, $titreProjet, $ordre, $heure, $salle, $observateur];
                }
                if ($_REQUEST['palmares'] == true){
                    return [$lettre, $titreProjet, $classement, $prix, $cadeau, $visite,];
                }
            }
            if (isset($_REQUEST['referrer'])) {
                if (substr(explode('&', $_REQUEST['referrer'])[3], -1) == 0) {
                    return [$lettre, $Academie, $Lycee, $titreProjet, $ordre, $heure, $salle, $observateur];
                }
                if (substr(explode('&', $_REQUEST['referrer'])[3], -1) == 0) {
                    return [$lettre, $titreProjet, $classement, $prix, $cadeau, $visite,];
                }
            }
        } elseif (Crud::PAGE_DETAIL === $pageName) {

            if ($_REQUEST['palmares'] == false) {
                return [$id, $lettre, $titreProjet, $ordre, $heure, $salle, $total, $classement, $rang, $nbNotes, $visite, $cadeau, $phrases, $liaison, $prix, $infoequipe, $eleves, $notess, $hote, $interlocuteur, $observateur];
            }
            if ($_REQUEST['palmares'] == true) {
                return [$lettre, $titreProjet, $classement, $prix, $cadeau, $visite,];
            }
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$lettre, $titreProjet, $ordre, $heure, $salle, $total, $classement, $rang, $nbNotes, $visite, $cadeau, $phrases, $liaison, $prix, $infoequipe, $eleves, $notess, $observateur];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            //dump($_REQUEST['referrer']);
            $tag = substr(explode('&', $_REQUEST['referrer'])[3], -1);
            // dd($tag);
            if ($tag == 1) {
                return [$lettre, $titreProjet, $classement, $prix, $cadeau, $visite,];
            } else {
                return [$lettre, $Academie, $Lycee, $titreProjet, $ordre, $heure, $salle,];
            }
        }
    }
        public function configureActions(Actions $actions): Actions
        {
            $actions = $actions
                ->add(Crud::PAGE_EDIT, Action::INDEX, 'Retour à la liste')


                ->remove(Crud::PAGE_INDEX, Action::NEW)
                ->remove(Crud::PAGE_INDEX, Action::DELETE)
                ->remove(Crud::PAGE_NEW, Action::SAVE_AND_ADD_ANOTHER);



            return $actions;

    }

}
