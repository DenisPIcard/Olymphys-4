<?php

namespace App\Controller\Admin;

use App\Controller\OdpfAdmin\AdminCKEditorField;
use App\Entity\Edition;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class AdminsiteCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Edition::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, 'Réglage du site')
            ->setSearchFields(['id', 'ed', 'ville', 'lieu'])
            ->setPaginatorPageSize(30)
            ->addFormTheme('@FOSCKEditor/Form/ckeditor_widget.html.twig')
                ;
    }

    public function configureFields(string $pageName): iterable
    {
        $ed = TextField::new('ed');
        $ville = TextField::new('ville');
        $date = DateTimeField::new('date');
        $lieu = TextField::new('lieu');
        $dateouverturesite = DateTimeField::new('dateouverturesite');
        $dateclotureinscription = DateTimeField::new('dateclotureinscription');
        $datelimcia = DateTimeField::new('datelimcia');
        $datelimnat = DateTimeField::new('datelimnat');
        $concourscia = DateField::new('concourscia');
        $concourscn = DateField::new('concourscn');

        $id = IntegerField::new('id', 'ID');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$ed, $ville, $date, $lieu, $dateouverturesite, $dateclotureinscription, $datelimcia, $datelimnat, $concourscia, $concourscn];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $ed, $date, $ville, $lieu, $datelimcia, $datelimnat, $dateouverturesite, $concourscia, $concourscn, $dateclotureinscription];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$ed, $ville, $date, $lieu, $dateouverturesite, $dateclotureinscription, $datelimcia, $datelimnat, $concourscia, $concourscn];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$ed, $ville, $date, $lieu, $dateouverturesite, $dateclotureinscription, $datelimcia, $datelimnat, $concourscia, $concourscn];
        }
    }
}
