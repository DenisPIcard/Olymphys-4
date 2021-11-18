<?php

namespace App\Controller\OdpfAdmin;

use App\Entity\OdpfArticle;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class OdpfArticleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return OdpfArticle::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            $titre = TextField::new('titre'),
            $choix = TextField::new('choix'),
            $texte = TextField::new('texte'),
            $id_categorie = AssociationField::new('catégorie'),
            $image = TextField::new('image'),
            $alt_image = TextField::new('alt_image'),
            $descr_image = TextField::new('description'),
            $titre_objectifs = TextField::new('titre_objectifs'),
            $texte_objectifs = TextField::new('texte_objectifs')

        ];
    }

}
