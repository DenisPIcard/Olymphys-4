<?php

namespace App\Controller\Admin;

use App\Entity\Photos;
use App\Controller\Admin\Filter\CustomCentreFilter;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Provider\AdminContextProvider;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class PhotosCrudController extends AbstractCrudController
{   private $session;
    private $adminContextProvider;
    public function __construct(SessionInterface $session,AdminContextProvider $adminContextProvider){
        $this->session=$session;
        $this->adminContextProvider=$adminContextProvider;

    }
    public static function getEntityFqcn(): string
    {
        return Photos::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, '<font color="green"><h2>Les photos des équipes</h2></font>')
            ->setPageTitle(Crud::PAGE_EDIT, 'Modifier une photo')
            ->setPageTitle(Crud::PAGE_NEW, 'Déposer une  photo')
            ->setSearchFields(['id', 'photo', 'coment'])
            ->setPaginatorPageSize(30)
            ->setFormThemes(['bundles/EasyAdminBundle/form_edit_photo.html.twig', '@EasyAdmin/crud/form_theme.html.twig']);

            //->overrideTemplate('crud/edit', 'bundles/EasyAdminBundle/edit_photo.html.twig');
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('edition'))
            ->add(EntityFilter::new('equipe'))
            ->add(CustomCentreFilter::new('centre'));
    }

    public function configureFields(string $pageName): iterable
    {   $context = $this->adminContextProvider->getContext();
        $concours = $context->getRequest()->query->get('concours');
        $panel1 = FormField::addPanel('<font color="red" > Choisir le fichier à déposer </font> ');
        $equipe = AssociationField::new('equipe');
        $photoFile = Field::new('photoFile');
        $panel2 = FormField::addPanel('<font color="red" > Choisir l\'équipe </font> ');
        $edition = AssociationField::new('edition');
        $id = IntegerField::new('id', 'ID');
        $photo = TextField::new('photo')->setTemplatePath('bundles\EasyAdminBundle\photos.html.twig')
            ->setFormTypeOptions(['block_name' => 'custom_photo', ]);;
        $coment = TextField::new('coment','commentaire');
        $national = Field::new('national');
        $updatedAt = DateTimeField::new('updatedAt', 'Déposé le ');
        $equipeCentreCentre = TextareaField::new('equipe.centre.centre','centre académique');
        $equipeNumero = IntegerField::new('equipe.numero','numéro');
        $equipeTitreprojet = TextareaField::new('equipe.titreprojet','Projet');
        $equipeLettre=TextField::new('equipe.lettre','Lettre');
        if (Crud::PAGE_INDEX === $pageName) {
            if ($concours=='interacadémique') {
                return [$edition, $equipeCentreCentre, $equipeNumero, $equipeTitreprojet, $photo, $updatedAt];
            }
            if ($concours=='national') {
                return [$edition, $equipeLettre, $equipeTitreprojet, $photo, $updatedAt];
            }
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $photo, $coment, $national, $updatedAt, $equipe, $edition];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$panel1, $photo, $equipe, $photoFile,$coment,$national, $coment];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$panel2, $photo, $equipe, $edition, $national,$coment];
        }
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder

    { //dd($context = $this->adminContextProvider->getContext());
        $context = $this->adminContextProvider->getContext();
        $repositoryEdition = $this->getDoctrine()->getManager()->getRepository('App:Edition');
        $repositoryCentrescia = $this->getDoctrine()->getManager()->getRepository('App:Centrescia');
        $concours = $context->getRequest()->query->get('concours');
        if ($concours == 'interacadémique') {
            $qb = $this->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters)
                ->andWhere('entity.national =:concours')
                ->setParameter('concours', 0);
        }
        if ($concours == 'national') {
            $qb = $this->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters)
                ->andWhere('entity.national =:concours')
                ->setParameter('concours', 1);

        }


        if ($context->getRequest()->query->get('filters') == null) {

            $qb->andWhere('entity.edition =:edition')
                ->setParameter('edition', $this->session->get('edition'));


        } else {
            if (isset($context->getRequest()->query->get('filters')['edition'])) {
                $idEdition = $context->getRequest()->query->get('filters')['edition']['value'];
                $edition = $repositoryEdition->findOneBy(['id' => $idEdition]);
                $this->session->set('titreedition', $edition);
            }
            if (isset($context->getRequest()->query->get('filters')['centre'])) {
                $idCentre = $context->getRequest()->query->get('filters')['centre'];
                $centre = $repositoryCentrescia->findOneBy(['id' => $idCentre]);
                $this->session->set('titrecentre', $centre);
                $qb->leftJoin('entity.equipe','eq')
                    ->andWhere('eq.centre =:centre')
                    ->setParameter('centre',$centre);
            }
            if (isset($context->getRequest()->query->get('filters')['equipe'])) {
                $idEquipe = $context->getRequest()->query->get('filters')['equipe']['value'];
                $equipe = $repositoryCentrescia->findOneBy(['id' => $idEquipe]);
                $this->session->set('titreequipe', $equipe);

            }
            //$qb = $this->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters);
        }
        $qb->leftJoin('entity.equipe', 'e');
        if ($concours == 'interacadémique') {
           $qb->addOrderBy('e.numero', 'ASC');
        }
        if ($concours == 'national') {
            $qb->addOrderBy('e.lettre', 'ASC');
        }
        return $qb;
}




}
