<?php


namespace App\Controller\Admin;
use App\Entity\Fichiersequipes;
use App\Controller\Admin\Field\AnnexeField;
use App\Service\valid_fichiers;
use App\Service\MessageFlashBag;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\HiddenField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Provider\AdminContextProvider;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\String\UnicodeString;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class FichiersequipesCrudController extends  AbstractCrudController
{   private $session;
    private $validator;
    private $adminContextProvider;
    private $flashbag;
    public function __construct(SessionInterface $session,AdminContextProvider $adminContextProvider,ValidatorInterface $validator, MessageFlashBag $flashBag){
        $this->session=$session;
        $this->adminContextProvider=$adminContextProvider;
        $this->validator=$validator;
        $this->flashbag=$flashBag;

    }
    public static function getEntityFqcn(): string
    {
        return Fichiersequipes::class;
    }
    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('edition'))
            ->add(EntityFilter::new('equipe'));
    }
    public function set_type_fichier($valueIndex,$valueSubIndex)
    {
        if ($valueIndex == 8) {
            switch ($valueSubIndex) {
                case 1 :
                    $typeFichier = 0; //mémoires ou annexes 1
                    break;
                case 2:
                    $typeFichier = 2;  //résumés
                    break;
                case 3 :
                    $typeFichier = 4; //Fiches sécurité
                    break;
                case 4 :
                    $typeFichier = 5; //Diaporamas interacadémiques
                    break;
                case 6 :
                    $typeFichier = 6; //Diaporamas interacadémiques
                    break;

            }
        }
        if ($valueIndex == 9)
            {
                switch ($valueSubIndex)
                {
                    case 3 :
                        $typeFichier = 0; //mémoires 0 ou annexes 1
                        break;
                    case 4:
                        $typeFichier = 2;  //résumés
                        break;
                    case 5 :
                        $typeFichier = 3; //Diaporama de la présentation nationale
                        break;
                }
            }
         return $typeFichier;
    }
    public function configureCrud(Crud $crud): Crud
    {
        $exp = new UnicodeString('<sup>e</sup>');
        $type = $this->set_type_fichier($_REQUEST['menuIndex'], $_REQUEST['submenuIndex']);
        if (!isset($_REQUEST['filters'])) {
           $edition = $this->session->get('edition');
        }
        else{

            if(isset($_REQUEST['filters']['equipe'])) {
                $equipeId= $_REQUEST['filters']['equipe']['value'];
                $equipe=$this->getDoctrine()->getManager()->getRepository('App:Equipesadmin')->findOneBy(['id'=>$equipeId]);
            }
            if(isset($_REQUEST['filters']['edition'])) {
                $editionId= $_REQUEST['filters']['edition']['value'];
                $edition=$this->getDoctrine()->getManager()->getRepository('App:Edition')->findOneBy(['id'=>$editionId]);
            }
            elseif(isset($_REQUEST['filters']['equipe'])){
                $edition = $equipe->getEdition();
            }


        }

        return $crud
            ->setPageTitle('index','Les '.$this->getParameter('type_fichier_lit')[$type].'s de la '.$edition->getEd().$exp.' édition')
            ->setPageTitle('new','')
            ->setPageTitle('edit','');
    }
    public function configureActions(Actions $actions): Actions
    {
        $typeFichier = $this->set_type_fichier($_REQUEST['menuIndex'], $_REQUEST['submenuIndex']);


        $actions = $actions
            ->add(Crud::PAGE_EDIT, Action::INDEX, 'Retour à la liste')
            ->add(Crud::PAGE_NEW, Action::INDEX, 'Retour à la liste')
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {

                return $action->setLabel('Déposer un fichier');
            });
        //->remove(Crud::PAGE_NEW, Action::NEW)
        if ($this->set_type_fichier($_REQUEST['menuIndex'], $_REQUEST['submenuIndex']) == 6) {
            $actions ->remove(Crud::PAGE_INDEX, Action::EDIT);
        ;
        }
        return $actions;
    }
    public function configureFields(string $pageName): iterable
    {
        if ($pageName==Crud::PAGE_NEW){

            $panel1 = FormField::addPanel('<font color="red" > Déposer un nouveau '.$this->getParameter('type_fichier_lit')[$this->set_type_fichier($_REQUEST['menuIndex'],$_REQUEST['submenuIndex'])].'  </font> ');
            $type=$this->set_type_fichier($_REQUEST['menuIndex'],$_REQUEST['submenuIndex']);

        }
        if ($pageName==Crud::PAGE_EDIT){

            $panel1 = FormField::addPanel('<font color="red" > Editer le fichier '.$this->getParameter('type_fichier_lit')[$this->set_type_fichier($_REQUEST['menuIndex'],$_REQUEST['submenuIndex'])].'  </font> ');
            $type=$this->set_type_fichier($_REQUEST['menuIndex'],$_REQUEST['submenuIndex']);

        }

        $equipe = AssociationField::new('equipe') ->setFormTypeOptions(['data_class'=> null])
                    ->setQueryBuilder(function ($queryBuilder) {
                    return $queryBuilder->select()->addOrderBy('entity.edition','DESC')->addOrderBy('entity.numero','ASC'); }
            );
        $fichierFile = Field::new('fichierFile','fichier')
            ->setFormType(VichFileType::class)
            ->setLabel('Fichier')
            ->onlyOnForms()
            ->setFormTypeOption('allow_delete',false);//sinon la case à cocher delete s'affiche
        switch ($this->set_type_fichier($_REQUEST['menuIndex'],$_REQUEST['submenuIndex'])){
            case 0 :$article= 'le';
                    break;
            case 1 :$article= 'le';
                    break;
            case 2 :$article= 'le';
                    break;
            case 3 :$article= 'le';
                break;
            case 4 :$article= 'la';
                break;
            case 5 :$article= 'le';
                break;
            case 6 :$article= 'l\'';
                break;
        }

        $panel2 = FormField::addPanel('<font color="red" > Modifier '.$article.' '.$this->getParameter('type_fichier_lit')[$this->set_type_fichier($_REQUEST['menuIndex'],$_REQUEST['submenuIndex'])] .'</font> ');
        $id = IntegerField::new('id', 'ID');
        $fichier = TextField::new('fichier')->setTemplatePath('bundles\\EasyAdminBundle\\liste_fichiers.html.twig');


        $typefichier = IntegerField::new('typefichier');
        if ($pageName==Crud::PAGE_INDEX){
            $context = $this->adminContextProvider->getContext();
            $context->getRequest()->query->set('concours',$_REQUEST['menuIndex']==8?0:1);
            $context->getRequest()->query->set('typefichier',$this->set_type_fichier($_REQUEST['menuIndex'],$_REQUEST['submenuIndex']));
        }
        $annexe= ChoiceField::new('typefichier','Mémoire ou annexe')
                ->setChoices(['Memoire'=>0,'Annexe'=>1])
                ->setFormTypeOptions(['required'=>true])
                ->setColumns('col-sm-4 col-lg-3 col-xxl-2') ;
        $national = BooleanField::new('national');
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
            if (($_REQUEST['submenuIndex']==1) or ($_REQUEST['submenuIndex']==3))
            {  return [$panel1, $equipe, $fichierFile, $annexe];}
            else {
                return [$panel1, $equipe, $fichierFile];
            }

        } elseif (Crud::PAGE_EDIT === $pageName) {
            if (($_REQUEST['submenuIndex']==1) or ($_REQUEST['submenuIndex']==3))
                {  return [$panel2, $equipe, $fichierFile, $annexe];}
            else {
                return [$panel2, $equipe, $fichierFile];
            }
        }
    }
    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {

        $context = $this->adminContextProvider->getContext();

        $repositoryEdition=$this->getDoctrine()->getManager()->getRepository('App:Edition');
        $repositoryCentrescia=$this->getDoctrine()->getManager()->getRepository('App:Centrescia');
        $typefichier=$this->set_type_fichier($_REQUEST['menuIndex'],$_REQUEST['submenuIndex']);

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
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {   //Nécessaire pour que les fichiers déjà existants d'une équipe soient écrasés, non pas ajoutés
        $validator = new valid_fichiers($this->validator);
        $dateconect = new \DateTime('now');
        $equipe = $entityInstance->getEquipe();
        $repositoryFichiers = $this->getDoctrine()->getManager()->getRepository('App:Fichiersequipes');
        $ErrorMessage = $validator->validation_fichiers($entityInstance->getFichierFile(), $this->set_type_fichier($_REQUEST['menuIndex'], $_REQUEST['submenuIndex']), $dateconect);
        if ($ErrorMessage != null) {
            $this->addFlash('alert', $ErrorMessage);
            //dd($ErrorMessage);

            $this->redirectToRoute('easyadmin', $_REQUEST);
        } else {
            $oldfichier = $repositoryFichiers->createQueryBuilder('f')
                ->where('f.equipe =:equipe')
                ->setParameter('equipe', $equipe)
                ->andWhere('f.typefichier =:typefichier')
                ->setParameter('typefichier', $entityInstance->getTypefichier())->getQuery()->getOneOrNUllResult();

            if (null !== $oldfichier) {

                $oldfichier->setFichierFile($entityInstance->getFichierFile());

                parent::persistEntity($entityManager, $oldfichier);
            } else {
                if ($_REQUEST['menuIndex'] == 8) {
                    $entityInstance->setNational(0);
                }
                if ($_REQUEST['menuIndex'] == 9) {
                    $entityInstance->setNational(1);
                }
                //$this->flashbag->addSuccess('Le fichier a bien été déposé');
                parent::persistEntity($entityManager, $entityInstance); // TODO: Change the autogenerated stub
            }
        }
    }

}
