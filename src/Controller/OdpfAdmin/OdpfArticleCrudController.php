<?php

namespace App\Controller\OdpfAdmin;

use App\Entity\OdpfArticle;
use App\Entity\OdpfCarousels;
use Doctrine\Persistence\ManagerRegistry;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class OdpfArticleCrudController extends AbstractCrudController
{

    private ManagerRegistry $doctrine;

    public function __construct( ManagerRegistry $doctrine)
    {

        $this->doctrine=$doctrine;

    }

    public static function getEntityFqcn(): string
    {
        return OdpfArticle::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->addFormTheme('@FOSCKEditor/Form/ckeditor_widget.html.twig');
    }

    public function configureFields(string $pageName): iterable
    {
        $listCarousels = $this->doctrine->getRepository(OdpfCarousels::class)->findAll();

        $titre = TextField::new('titre');
        $choix = TextField::new('choix');
        $texte = AdminCKEditorField::new('texte');
        $categorie = AssociationField::new('categorie');
        $alt_image = TextField::new('alt_image');
        $descr_image = AdminCKEditorField::new('descr_image');
        $titre_objectifs = TextField::new('titre_objectifs');
        $texte_objectifs = AdminCKEditorField::new('texte_objectifs');
        $carousel = AssociationField::new('carousel')->setFormTypeOptions(['choices' => $listCarousels]);
        $updatedAt = DateTimeField::new('updatedAt');
        $updatedat = DateTimeField::new('updatedat', 'Mis à jour  le ');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$titre, $choix, $texte, $categorie, $alt_image, $descr_image, $titre_objectifs, $texte_objectifs, $carousel, $updatedat];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$titre, $choix, $texte, $categorie, $alt_image, $descr_image, $titre_objectifs, $texte_objectifs, $carousel, $updatedAt];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$titre, $choix, $texte, $categorie, $alt_image, $descr_image, $titre_objectifs, $texte_objectifs, $carousel];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$titre, $choix, $texte, $categorie, $alt_image, $descr_image, $titre_objectifs, $texte_objectifs, $carousel];
        }


    }

}
