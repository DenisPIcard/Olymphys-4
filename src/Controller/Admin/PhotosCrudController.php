<?php

namespace App\Controller\Admin;

use App\Entity\Photos;
use App\Controller\Admin\Filter\CustomCentreFilter;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;

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
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;


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
            ->setFormThemes(['@EasyAdmin/crud/form_theme.html.twig']);
            //->overrideTemplates(['crud/index'=>'bundles/EasyAdminBundle/custom/index.html.twig','crud/edit'=>'bundles/EasyAdminBundle/custom/edit.html.twig']);
            //->overrideTemplate('crud/edit', 'bundles/EasyAdminBundle/custom/edit.html.twig');
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('edition'))
            ->add(EntityFilter::new('equipe'))
            ->add(CustomCentreFilter::new('centre'));
    }
    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_EDIT, Action::INDEX)
            ->add(Crud::PAGE_NEW, Action::INDEX)
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setLabel('Déposer une photo');
            });
    }

    public function configureFields(string $pageName): iterable
    {



        $context = $this->adminContextProvider->getContext();


        $panel1 = FormField::addPanel('<font color="red" > Choisir le fichier à déposer </font> ');
        $equipe = AssociationField::new('equipe')
            ->setFormTypeOptions(['data_class'=> null])
            ->setQueryBuilder(function ($queryBuilder) {
                return $queryBuilder->select()->addOrderBy('entity.edition','DESC')->addOrderBy('entity.numero','ASC'); }
            );
        $edition = AssociationField::new('edition');
        $id = IntegerField::new('id', 'ID');
        $photo = TextField::new('photo')
            ->setTemplatePath('bundles\EasyAdminBundle\photos.html.twig')
            ->setLabel('Nom de la photo')
            ->setFormTypeOption('disabled','disabled');
           //
            
        $coment = TextField::new('coment','commentaire');
        $national = Field::new('national')
                    ->setValue(false);
        $updatedAt = DateTimeField::new('updatedAt', 'Déposé le ');


        $equipeCentreCentre = TextareaField::new('equipe.centre.centre','centre académique');
        $equipeNumero = IntegerField::new('equipe.numero','numéro');
        $equipeTitreprojet = TextareaField::new('equipe.titreprojet','Projet');
        $equipeLettre=TextField::new('equipe.lettre','Lettre');
        $imageFile= Field::new('photoFile')
                ->setFormType(VichImageType::class)
                ->setLabel('Photo')
                ->onlyOnForms()
                ->setFormTypeOption('allow_delete',false)
                ;

        if (Crud::PAGE_INDEX === $pageName) {
           if ($context->getRequest()->query->get('menuIndex')==8) {
                return [$edition, $equipeCentreCentre, $equipeNumero, $equipeTitreprojet, $photo, $coment, $updatedAt];
            }
           if ($context->getRequest()->query->get('menuIndex')==9) {
                return [$edition, $equipeLettre, $equipeTitreprojet, $photo, $coment, $updatedAt];
            }

        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $photo, $coment, $national, $updatedAt, $equipe, $edition];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$panel1, $equipe, $imageFile,$coment,$national, $coment];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [ $photo,$imageFile,$equipe,  $national, $coment];
        }
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder

    { //dd($context = $this->adminContextProvider->getContext());
        $context = $this->adminContextProvider->getContext();
        $repositoryEdition = $this->getDoctrine()->getManager()->getRepository('App:Edition');
        $repositoryCentrescia = $this->getDoctrine()->getManager()->getRepository('App:Centrescia');
        $concours = $context->getRequest()->query->get('concours');
        if ($context->getRequest()->query->get('menuIndex')==8)  {
            $qb = $this->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters)
                ->andWhere('entity.national =:concours')
                ->setParameter('concours', 0);
        }
        if ($context->getRequest()->query->get('menuIndex')==9) {
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

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {   $edition=$entityInstance->getEquipe()->getEdition();
        $entityInstance->setEdition($edition);
        $entityManager->persist($entityInstance);
        $entityManager->flush();
    }



    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
       /* $name=$entityInstance->getPhoto();
        rename('upload/photos/'.$entityInstance->getPhoto(),  'upload/photos/'.$name);
        rename('upload/photos/thumbs/'.$entityInstance->getPhoto(),  'upload/photos/thumbs/'.$name);
        $entityInstance->setPhoto($name);*/
        $entityManager->persist($entityInstance);
        $entityManager->flush();
    }



}
