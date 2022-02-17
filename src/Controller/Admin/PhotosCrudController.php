<?php

namespace App\Controller\Admin;

use App\Entity\Photos;

use App\Controller\Admin\Filter\CustomCentreFilter;
use App\Service\ImagesCreateThumbs;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Form;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\EaFormPanelType;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use PhpParser\Node\Stmt\Label;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Validator\Constraints\File;

use Symfony\Component\Validator\Constraints\NotBlank;

use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;

use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Provider\AdminContextProvider;
use phpDocumentor\Reflection\Types\Collection;
use PhpOffice\PhpWord\Style\Image;
use Symfony\Component\Form\Extension\Core\Type\FileType;
//use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RequestStack;
use Vich\UploaderBundle\Form\Type\VichImageType;


class PhotosCrudController extends AbstractCrudController
{   private $requestStack;
    private $adminContextProvider;
    public function __construct(RequestStack $requestStack,AdminContextProvider $adminContextProvider){
        $this->requestStack=$requestStack;;
        $this->adminContextProvider=$adminContextProvider;

    }
    public static function getEntityFqcn(): string
    {
        return Photos::class;
    }

    public function configureCrud(Crud $crud): Crud
    {    $concours =$this->requestStack->getCurrentRequest()->query->get('concours');
        if ($concours==null){
            $_REQUEST['menuIndex']==10?$concours=1:$concours=0;
            $concours==1?$concours='national':$concours='interacadémique';
        }

        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, '<font color="green"><h2>Les photos du concours '.$concours.'</h2></font>')
            ->setPageTitle(Crud::PAGE_EDIT, 'Modifier une photo du concours '.$concours)
            ->setPageTitle(Crud::PAGE_NEW, 'Déposer une  photo du concours '.$concours)
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
    {   //$concours =$this->requestStack->getCurrentRequest()->query->get('concours');
        return $actions
            ->add(Crud::PAGE_EDIT, Action::INDEX)
            ->remove(Crud::PAGE_EDIT, Action::SAVE_AND_CONTINUE)
            ->add(Crud::PAGE_NEW, Action::INDEX)
            ->remove(Crud::PAGE_NEW,Action::SAVE_AND_ADD_ANOTHER)
            ->update(Crud::PAGE_NEW, Action::SAVE_AND_RETURN, function (Action $action) {
                return $action->setLabel('Déposer')->setHtmlAttributes(['concours'=> $this->requestStack->getCurrentRequest()->query->get('concours')]);
            })
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setLabel('Déposer une photo')->setHtmlAttributes(['concours'=> $this->requestStack->getCurrentRequest()->query->get('concours')]);
            });
    }

    public function configureFields(string $pageName): iterable
    {


        $concours=$this->requestStack->getCurrentRequest()->query->get('concours');
        if ($concours==null){
           $_REQUEST['menuIndex']==10?$concours='national':$concours='interacadémique';
        }
        $context = $this->adminContextProvider->getContext();

        $panel1 = FormField::addPanel('<font color="red" > Choisir le fichier à déposer </font> ');
        $equipe = AssociationField::new('equipe')
            ->setFormTypeOptions(['data_class'=> null])
            ->setQueryBuilder(function ($queryBuilder) {
                $_REQUEST['menuIndex']==10?$concours='national':$concours='interacadémique';
                $concours=='national'?$tag=1:$tag=0;

                return $queryBuilder->select()->andWhere('entity.edition =:edition')
                    ->andWhere('entity.selectionnee =:selectionnee ')
                    ->setParameter('edition',$this->requestStack->getSession()->get('edition'))
                    ->setParameter('selectionnee',$tag)
                    ->addOrderBy('entity.edition','DESC')
                    ->addOrderBy('entity.lettre','ASC')
                    ->addOrderBy('entity.numero','ASC'); }
            );
        $edition = AssociationField::new('edition');
        $id = IntegerField::new('id', 'ID');
        $photo = TextField::new('photo')
            ->setTemplatePath('bundles\EasyAdminBundle\photos.html.twig')
            ->setLabel('Nom de la photo')
            ->setFormTypeOption('disabled','disabled');
           //
            
        $coment = TextField::new('coment','commentaire');
        $concours=='national'? $valnat=true:$valnat=false;
        $national = Field::new('national')->setFormTypeOption('data',$valnat);

        $updatedAt = DateTimeField::new('updatedAt', 'Déposé le ');


        $equipeCentreCentre = TextareaField::new('equipe.centre.centre','centre académique');
        $equipeNumero = IntegerField::new('equipe.numero','numéro');
        $equipeTitreprojet = TextareaField::new('equipe.titreprojet','Projet');
        $equipeLettre=TextField::new('equipe.lettre','Lettre');
        $imageFile= Field::new('photoFile')
                ->setFormType(FileType::class)
                ->setLabel('Photo')
                ->onlyOnForms()
                /*->setFormTypeOption('constraints', [
                            'mimeTypes' => ['image/jpeg','image/jpg'],
                            'mimeTypesMessage' => 'Please upload a valid PDF document',
                            'data_class'=>'photos'
                    ]
                )*/
                ;
        /*$imagesMultiples=CollectionField::new('photoFile')
            ->setLabel('Photo(s)')

            ->onlyOnForms()
            ->setFormTypeOptions(['by_reference'=>false])
            ;*/

        if (Crud::PAGE_INDEX === $pageName) {
           if ($concours=='interacadémique') {
                return [$edition, $equipeCentreCentre, $equipeNumero, $equipeTitreprojet, $photo, $coment, $updatedAt];
            }
           if ($concours=='national') {
                return [$edition, $equipeLettre, $equipeTitreprojet, $photo, $coment, $updatedAt];
            }

        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $photo, $coment, $national, $updatedAt, $equipe, $edition];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$panel1, $equipe, $imageFile,$coment,$national, $coment];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            $this->requestStack->getCurrentRequest()->query->set('concours',$concours);
            return [ $photo,$imageFile,$equipe,  $national, $coment];
        }
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder

    {   //$concours =$this->requestStack->getCurrentRequest()->query->get('concours');

        //if (null==$concours){
            $_REQUEST['menuIndex']==10?$concours='national':$concours='interacadémique';
        //}

        $session=$this->requestStack->getSession();
        $context = $this->adminContextProvider->getContext();
        $repositoryEdition = $this->getDoctrine()->getManager()->getRepository('App:Edition');
        $repositoryCentrescia = $this->getDoctrine()->getManager()->getRepository('App:Centrescia');

        if ($concours=='interacadémique')  {
            $qb = $this->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters)
                ->andWhere('entity.national =:concours')
                ->setParameter('concours', 0);
        }
        if ($concours== 'national') {
            $qb = $this->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters)
                ->andWhere('entity.national =:concours')
                ->setParameter('concours', 1);

        }


        if ($context->getRequest()->query->get('filters') == null) {

            $qb->andWhere('entity.edition =:edition')
                ->setParameter('edition', $session->get('edition'));


        } else {
            if (isset($context->getRequest()->query->get('filters')['edition'])) {
                $idEdition = $context->getRequest()->query->get('filters')['edition']['value'];
                $edition = $repositoryEdition->findOneBy(['id' => $idEdition]);
               $session->set('titreedition', $edition);
            }
            if (isset($context->getRequest()->query->get('filters')['centre'])) {
                $idCentre = $context->getRequest()->query->get('filters')['centre'];
                $centre = $repositoryCentrescia->findOneBy(['id' => $idCentre]);
               $session->set('titrecentre', $centre);
                $qb->leftJoin('entity.equipe','eq')
                    ->andWhere('eq.centre =:centre')
                    ->setParameter('centre',$centre);
            }
            if (isset($context->getRequest()->query->get('filters')['equipe'])) {
                $idEquipe = $context->getRequest()->query->get('filters')['equipe']['value'];
                $equipe = $repositoryCentrescia->findOneBy(['id' => $idEquipe]);
               $session->set('titreequipe', $equipe);

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
    {

        $edition=$entityInstance->getEquipe()->getEdition();
        $entityInstance->setEdition($edition);
        $entityManager->persist($entityInstance);
        $entityManager->flush();

    }

   public function getRedirectResponseAfterSave(AdminContext $context, string $action): RedirectResponse
   {
       $this->addFlash('info', 'La photo a bien été déposée');
       //concours=interacadémique&crudAction=index&crudControllerFqcn=App\Controller\Admin\PhotosCrudController&entityFqcn=App\Entity\Photos&menuIndex=9&page=1&referrer=%2Fadmin%3Fconcours%3Dinteracad%25C3%25A9mique%26crudAction%3Dindex%26crudControllerFqcn%3DApp%255CController%255CAdmin%255CPhotosCrudController%26entityFqcn%3DApp%255CEntity%255CPhotos%26menuIndex%3D9%26signature%3DD_dbqZBiCTL2u86pkJe7RoKA3ec0y2RxUmTVhNoMeoA%26submenuIndex%3D7&signature=D_dbqZBiCTL2u86pkJe7RoKA3ec0y2RxUmTVhNoMeoA&sort[updatedAt]=DESC&submenuIndex=7
       if ($_REQUEST['menuIndex'] == 9) {

       return $this->redirectToRoute('admin', ['concours' => 'interacadémique', 'crudAction' => 'index', 'crudControllerFqcn' => 'App\Controller\Admin\PhotosCrudController', 'entityFqcn' => 'App\Entity\Photos', 'menuIndex' => 9, 'page' => 1, 'signature' => 'D_dbqZBiCTL2u86pkJe7RoKA3ec0y2RxUmTVhNoMeoA', 'sort[updatedAt]' => 'DESC', 'submenuIndex' => 7]); // TODO: Change the autogenerated stub
        }
       if ($_REQUEST['menuIndex'] == 10) {

           return $this->redirectToRoute('admin', ['concours' => 'national', 'crudAction' => 'index', 'crudControllerFqcn' => 'App\Controller\Admin\PhotosCrudController', 'entityFqcn' => 'App\Entity\Photos', 'menuIndex' => 10, 'page' => 1, 'signature' => 'D_dbqZBiCTL2u86pkJe7RoKA3ec0y2RxUmTVhNoMeoA', 'sort[updatedAt]' => 'DESC', 'submenuIndex' => 7]); // TODO: Change the autogenerated stub
       }
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
