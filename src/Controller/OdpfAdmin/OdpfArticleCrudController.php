<?php

namespace App\Controller\OdpfAdmin;

use App\Entity\OdpfArticle;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use App\Controller\OdpfAdmin\CKEditorField;

class OdpfArticleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return OdpfArticle::class;
    }
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->addFormTheme('@FOSCKEditor/Form/ckeditor_widget.html.twig')
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            //$id = IntegerField::new('id'),
            $titre = TextEditorField::new('titre'),
            $choix = TextField::new('choix'),
            $texte = AdminCKEditorField::new('texte'),
            $id_categorie = TextField::new('id_categorie'),
            $image = TextField::new('image'),
            $alt_image = TextField::new('alt_image'),
            $descr_image = TextEditorField::new('descr_image'),
            $titre_objectifs = TextEditorField::new('titre_objectifs'),
            $texte_objectifs = TextEditorField::new('texte_objectifs')

        ];
    }

}
