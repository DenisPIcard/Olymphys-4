<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Filter\ElevesinterFilter;
use App\Entity\Elevesinter;
use App\Form\Filter\ElevesinterFilterType;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Provider\AdminContextProvider;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ElevesinterCrudController extends AbstractCrudController
{   private $session;
    private $adminContextProvider;
    public function __construct(SessionInterface $session,AdminContextProvider $adminContextProvider)
    {
        $this->session = $session;
        $this->adminContextProvider = $adminContextProvider;
    }
        public static function getEntityFqcn(): string
    {
        return Elevesinter::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setSearchFields(['id', 'numsite', 'nom', 'prenom', 'genre', 'classe', 'courriel']);
            //->overrideTemplate('crud/index', 'Admin/customizations/list_eleves.html.twig');
    }

    public function configureFilters(Filters $filters): Filters
    {
        $filter= $filters
            ->add(ElevesinterFilter::new('equipe'));


        return $filters;

    }

    public function configureFields(string $pageName): iterable
    {
        $nom = TextField::new('nom');
        $prenom = TextField::new('prenom');
        $genre = TextField::new('genre');
        $courriel = TextField::new('courriel');
        $equipe = AssociationField::new('equipe');
        $id = IntegerField::new('id', 'ID');
        $numsite = IntegerField::new('numsite');
        $classe = TextField::new('classe');
        $autorisationphotos = AssociationField::new('autorisationphotos');
        $prenom = TextareaField::new('prenom ');
        $equipeNumero = IntegerField::new('equipe.numero', ' Numéro équipe');
        $equipeTitreProjet = TextareaField::new('equipe.titreProjet');
        $equipeLyceeLocalite = TextareaField::new('equipe.lyceeLocalite', 'ville');
        $autorisationphotosFichier = TextareaField::new('autorisationphotos.fichier');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$nom, $prenom, $genre, $courriel, $equipeNumero, $equipeTitreProjet, $equipeLyceeLocalite, $autorisationphotosFichier];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $numsite, $nom, $prenom, $genre, $classe, $courriel, $equipe, $autorisationphotos];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$nom, $prenom, $genre, $courriel, $equipe];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$nom, $prenom, $genre, $courriel, $equipe];
        }
    }
    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $context = $this->adminContextProvider->getContext();
        $repositoryEdition=$this->getDoctrine()->getManager()->getRepository('App:Edition');
        $repositoryCentrescia=$this->getDoctrine()->getManager()->getRepository('App:Centrescia');
        if ($context->getRequest()->query->get('filters') == null) {

            $qb = $this->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters)
                ->leftJoin('entity.equipe','eq')
                ->andWhere('eq.edition =:edition')
                ->setParameter('edition', $this->session->get('edition'));

        }


        else{
            if (isset($context->getRequest()->query->get('filters')['edition'])){
                $idEdition=$context->getRequest()->query->get('filters')['edition']['value'];
                $edition=$repositoryEdition->findOneBy(['id'=>$idEdition]);
                $this->session->set('titreedition',$edition);}
            if (isset($context->getRequest()->query->get('filters')['centre'])){
                $idCentre=$context->getRequest()->query->get('filters')['centre']['value'];
                $centre=$repositoryCentrescia->findOneBy(['id'=>$idCentre]);
                $this->session->set('titrecentre',$centre);}
            $qb = $this->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters);
        }

        return $qb;
    }
}
