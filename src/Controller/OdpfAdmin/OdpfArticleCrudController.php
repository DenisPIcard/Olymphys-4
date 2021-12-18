<?php

namespace App\Controller\OdpfAdmin;

use App\Controller\OdpfAdmin\AdminCKEditorField;
use App\Entity\OdpfArticle;
use App\Entity\OdpfCarousels;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

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
    {   $listCarousels=$this->getDoctrine()->getRepository(OdpfCarousels::class)->findAll();

        return [
            $titre = TextField::new('titre'),
            $choix = TextField::new('choix'),
            $texte = AdminCKEditorField::new('texte'),
            $id_categorie = TextField::new('id_categorie'),
            $image = TextField::new('image'),
            $alt_image = TextField::new('alt_image'),
            $descr_image = AdminCKEditorField::new('descr_image'),
            $titre_objectifs = AdminCKEditorField::new('titre_objectifs'),
            $texte_objectifs = AdminCKEditorField::new('texte_objectifs'),
            $carousel=AssociationField::new('carousel')->setFormTypeOptions(['choices'=>$listCarousels]),

        ];
    }

}
