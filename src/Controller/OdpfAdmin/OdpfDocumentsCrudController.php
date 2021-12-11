<?php

namespace App\Controller\OdpfAdmin;

use App\Entity\OdpfDocuments;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use App\Form\VichFilesField;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;


class OdpfDocumentsCrudController extends AbstractCrudController
{
    private $params;
    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }


    public static function getEntityFqcn(): string
    {
        return OdpfDocuments::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('OdpfDocuments')
            ->setEntityLabelInPlural('OdpfDocuments')
            ->setPageTitle(Crud::PAGE_INDEX, '<h2>Les documents pour le site</h2>')
            ->setPageTitle(Crud::PAGE_EDIT, 'Editer le document')
            ->setPageTitle(Crud::PAGE_NEW, 'Nouveau document')
            ->setSearchFields(['id', 'fichier', 'type', 'titre', 'description'])
            ->setPaginatorPageSize(10);
    }

    public function configureFields(string $pageName): iterable
    {
        $type = ChoiceField::new('type')->setChoices( ['Zip' => 'zip', 'pdf' => 'pdf', 'doc'=>'doc']);
        $titre = TextField::new('titre');
        $description = TextField::new('description');
        $fichierFile = VichFilesField::new('fichierFile')->setBasePath($this->params->get('app.path.odpf_documents.localhost'));
        $id = IntegerField::new('id', 'ID');
        $fichier = TextField::new('fichier')->setTemplatePath('bundles\\EasyAdminBundle\\odpf\\liste_odpf_documents.html.twig');
        $updatedAt = DateTimeField::new('updatedAt');
        $updatedat = DateTimeField::new('updatedat', 'Mis à jour  le ');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$type, $titre, $description, $fichier, $updatedat];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $fichier, $updatedAt, $type, $titre, $description];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$type, $titre, $description, $fichierFile];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$type, $titre, $description, $fichierFile];
        }
    }
}

