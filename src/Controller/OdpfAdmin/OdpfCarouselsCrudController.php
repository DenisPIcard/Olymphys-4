<?php

namespace App\Controller\OdpfAdmin;

use App\Entity\OdpfCarousels;
use App\Entity\OdpfImagescarousels;
use App\Form\OdpfImagesType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class OdpfCarouselsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return OdpfCarousels::class;
    }


   public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name','nom'),
            CollectionField::new('images')->setEntryType(OdpfImagesType::class)->setTemplatePath('bundle/EasyAdminBundle/odpf/odpf_images_carousels.html.twig'),
            ChoiceField::new('categorie')->setChoices([ 'concours'=>1, 'olympiades'=>2])
        ];
    }

}
