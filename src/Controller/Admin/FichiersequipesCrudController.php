<?php


namespace App\Controller\Admin;
use App\Entity\Fichiersequipes;
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

class FichiersequipesCrudController extends  AbstractCrudController
{   private $session;
    private $adminContextProvider;
    public function __construct(SessionInterface $session,AdminContextProvider $adminContextProvider){
        $this->session=$session;
        $this->adminContextProvider=$adminContextProvider;

    }
    public static function getEntityFqcn(): string
    {
        return Fichiersequipes::class;
    }
    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('edition'));
    }

    public function configureFields(string $pageName): iterable
    {
        $panel1 = FormField::addPanel('<font color="red" > Déposer un nouvau résumé </font> ');
        $equipe = AssociationField::new('equipe');
        $fichierFile = Field::new('fichier','fichier');;
        $panel2 = FormField::addPanel('<font color="red" > Modifier le résumé </font> ');
        $id = IntegerField::new('id', 'ID');
        $fichier = TextField::new('fichier')->setTemplatePath('bundles\\EasyAdminBundle\\liste_fichiers.html.twig');
        $typefichier = IntegerField::new('typefichier');
        $national = Field::new('national');
        $updatedAt = DateTimeField::new('updatedAt');
        $nomautorisation = TextField::new('nomautorisation');
        $edition = AssociationField::new('edition');
        $eleve = AssociationField::new('eleve');
        $prof = AssociationField::new('prof');
        $editionEd = TextareaField::new('edition.ed');
        $equipeNumero = IntegerField::new('equipe.numero');
        $equipeLettre = TextareaField::new('equipe.lettre');
        $equipeCentreCentre = TextareaField::new('equipe.centre.centre');
        $equipeTitreprojet = TextareaField::new('equipe.titreprojet');
        $updatedat = DateTimeField::new('updatedat', 'Déposé le ');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$editionEd, $equipeNumero, $equipeLettre, $equipeTitreprojet, $fichier, $updatedat];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $fichier, $typefichier, $national, $updatedAt, $nomautorisation, $edition, $equipe, $eleve, $prof];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$panel1, $equipe, $fichierFile];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$panel2, $equipe, $fichierFile];
        }
    }
    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    { //dd($context = $this->adminContextProvider->getContext());
        $context = $this->adminContextProvider->getContext();
        $repositoryEdition=$this->getDoctrine()->getManager()->getRepository('App:Edition');
        $repositoryCentrescia=$this->getDoctrine()->getManager()->getRepository('App:Centrescia');
        $typefichier=$context->getRequest()->query->get('typefichier');
        $concours=$context->getRequest()->query->get('concours');
        if ($typefichier==0) {
               $qb = $this->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters)
                   ->andWhere('entity.typefichier <=:typefichier')
                   ->setParameter('typefichier', $typefichier+1);
        }
        if ($typefichier>1) {
            $qb = $this->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters)
                ->andWhere('entity.typefichier =:typefichier')
                ->setParameter('typefichier', $typefichier);

        }


        if ($context->getRequest()->query->get('filters') == null) {

                $qb->andWhere('entity.edition =:edition')
                    ->setParameter('edition', $this->session->get('edition'));


        }
        else{
            if (isset($context->getRequest()->query->get('filters')['edition'])){
                $idEdition=$context->getRequest()->query->get('filters')['edition']['value'];
                $edition=$repositoryEdition->findOneBy(['id'=>$idEdition]);
                $this->session->set('titreedition',$edition);
            }
            if (isset($context->getRequest()->query->get('filters')['centre'])){
                $idCentre=$context->getRequest()->query->get('filters')['centre']['value'];
                $centre=$repositoryCentrescia->findOneBy(['id'=>$idCentre]);
                $this->session->set('titrecentre',$centre);

            }
            //$qb = $this->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters);
        }
        $qb->andWhere('entity.national =:concours')
            ->setParameter('concours',$concours)
            ->leftJoin('entity.equipe','e');
        if ($concours==0){
            $qb->addOrderBy('e.numero','ASC');}
        if ($concours==1) {
            $qb-> addOrderBy('e.lettre', 'ASC');
        }
        return $qb;
    }
}
