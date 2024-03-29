<?php


namespace App\Controller\Admin;
use App\Entity\Fichiersequipes;
use App\Entity\Edition;
use App\Controller\Admin\Field\AnnexeField;
use App\Service\valid_fichiers;
use App\Service\MessageFlashBag;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Parameter;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterCrudActionEvent;
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
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use PhpOffice\PhpWord\Shared\ZipArchive;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\String\UnicodeString;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Symfony\Component\Routing\Annotation\Route;
use Vich\UploaderBundle\Form\Type\VichImageType;

class FichiersequipesCrudController extends  AbstractCrudController
{   private $requestStack;
    private $validator;
    private $adminContextProvider;
    private $flashbag;
    private $parameterBag;
    private $em;
    public function __construct(RequestStack $requestStack,AdminContextProvider $adminContextProvider,ValidatorInterface $validator, EntityManagerInterface $entitymanager,MessageFlashBag $flashBag,ParameterBagInterface $parameterBag){
        $this->requestStack=$requestStack;;
        $this->adminContextProvider=$adminContextProvider;
        $this->validator=$validator;
        $this->flashbag=$flashBag;
        $this->parameterBag=$parameterBag;
        $this->em=$entitymanager;

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
        if ($valueIndex == 9) {
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
                case 8:
                    $typeFichier = 7; //Questionnaires interacadémiques
                    break;
            }
        }
        if ($valueIndex == 10)
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
                    case 8 :
                        $typeFichier = 4; //Fiches sécurités des équipes sélectionnées
                        break;
                }
            }
         return $typeFichier;
    }
    public function configureCrud(Crud $crud): Crud
    {


        $session=$this->requestStack->getSession();
        $exp = new UnicodeString('<sup>e</sup>');

        $typefichier = $this->requestStack->getMainRequest()->query->get('typefichier');
        if ($typefichier==null){

            $typefichier=$this->set_type_fichier($_REQUEST['menuIndex'],$_REQUEST['submenuIndex']);
        }
        $concours= $this->requestStack->getMainRequest()->query->get('concours');
        if ($concours==null){
            $_REQUEST['menuIndex']==10?$concours=1:$concours=0;
        }
        $pageName=$this->requestStack->getMainRequest()->query->get('crudAction');
        if (!isset($_REQUEST['filters'])) {
            $edition = $session->get('edition');
        } else {

            if (isset($_REQUEST['filters']['equipe'])) {
                $equipeId = $_REQUEST['filters']['equipe']['value'];
                $equipe = $this->getDoctrine()->getManager()->getRepository('App:Equipesadmin')->findOneBy(['id' => $equipeId]);
            }
            if (isset($_REQUEST['filters']['edition'])) {
                $editionId = $_REQUEST['filters']['edition']['value'];
                $edition = $this->getDoctrine()->getManager()->getRepository('App:Edition')->findOneBy(['id' => $editionId]);
            } elseif (isset($_REQUEST['filters']['equipe'])) {
                $edition = $equipe->getEdition();
            }


        }

        $concours==1?$concourslit='national':$concourslit='interacadémique';
        if($pageName=='index') {
            if (($typefichier == 0) | ($typefichier == 2)) {
               //dump($typefichier);
               //dump($edition->getEd());
                //dd('Les ' . $this->getParameter('type_fichier_lit')[$typefichier] . 's de la ' . $edition->getEd() . $exp . ' édition');
                $crud = $crud->setPageTitle('index', 'Les ' . $this->getParameter('type_fichier_lit')[$typefichier] . 's de la ' . $edition->getEd() . $exp . ' édition. Concours '.$concourslit);
            }

            if ($typefichier == 3) {
                $crud = $crud->setPageTitle('index', 'Les diaporamas(concours national) de la ' . $edition->getEd() . $exp . ' édition');
            }
            if ($typefichier == 4) {
                $crud = $crud->setPageTitle('index', 'Les fiches sécurité de la ' . $edition->getEd() . $exp . ' édition du concours '.$concourslit);
            }
            if ($typefichier == 5) {
                $crud = $crud->setPageTitle('index', 'Les diaporamas(pour le concours interacadémique) de la ' . $edition->getEd() . $exp . ' édition');
            }
            if ($typefichier == 6) {
                $crud = $crud->setPageTitle('index', 'Les autorisations photos de la ' . $edition->getEd() . $exp . ' édition');
            }
            if ($typefichier == 7) {
                $crud = $crud->setPageTitle('index', 'Les questionnaires de la ' . $edition->getEd() . $exp . ' édition');
            }
        }
        $crud->setPageTitle('new','')
            ->setPageTitle('edit','');
        $_REQUEST['typefichier']=$typefichier;
        $_REQUEST['concours']=$concours;
        return $crud;
    }
    public function configureActions(Actions $actions): Actions
    {       $equipeId='na';
            $editionId='na';

        if(isset($_REQUEST['filters'])){
            if (isset($_REQUEST['filters']['edition'])){
                $editionId=$_REQUEST['filters']['edition']['value'];
            }
            if (isset($_REQUEST['filters']['equipe'])){
                $equipeId=$_REQUEST['filters']['equipe']['value'];

            }
        }
        $typefichier = $this->set_type_fichier($_REQUEST['menuIndex'], $_REQUEST['submenuIndex']);


        $telechargerFichiers = Action::new('telecharger', 'Télécharger  les fichiers', 'fa fa-file-download')
            ->linkToRoute('telechargerFichiers',['ideditionequipe' => $editionId.'-'.$equipeId])
            ->createAsGlobalAction();
            //->displayAsButton()            ->setCssClass('btn btn-primary');;

        $newFichier= Action::new('deposer','Déposer un fichier')->linkToCrudAction('new')->setHtmlAttributes(['typefichier'=>$typefichier])->createAsGlobalAction();

        $actions = $actions
            ->add(Crud::PAGE_EDIT, Action::INDEX, 'Retour à la liste')
            ->add(Crud::PAGE_NEW, Action::INDEX, 'Retour à la liste')
            ->update(Crud::PAGE_NEW,Action::SAVE_AND_RETURN,function (Action $action) {
                return $action->setLabel('Déposer le fichier');})
            ->add(Crud::PAGE_INDEX,$telechargerFichiers)
            ->add(Crud::PAGE_INDEX,$newFichier)
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_INDEX,Action::EDIT)
            ->remove(Crud::PAGE_NEW, Action::SAVE_AND_ADD_ANOTHER);

        if ($this->set_type_fichier($_REQUEST['menuIndex'], $_REQUEST['submenuIndex']) == 6) {
            //$actions ->remove(Crud::PAGE_INDEX, Action::EDIT)
                        //;
        ;
        }

        return $actions;
    }
    /**
     *@Route("/Admin/FichiersequipesCrud/telechargerFichierss,{ideditionequipe}", name="telechargerFichiers")
     */
    public function telechargerFichiers(AdminContext $context,$ideditionequipe)
    {
        $session=$this->requestStack->getSession();
        $typefichier=$this->set_type_fichier($_REQUEST['menuIndex'],$_REQUEST['submenuIndex']);
        $repositoryEquipe = $this->getDoctrine()->getRepository('App:Equipesadmin');
        $repositoryEdition = $this->getDoctrine()->getRepository('App:Edition');
        $idEdition=explode('-',$ideditionequipe)[0];
        $idEquipe=explode('-',$ideditionequipe)[1];

        $qb =$this->getDoctrine()->getManager()->getRepository('App:Fichiersequipes')->CreateQueryBuilder('f');
        if ($typefichier==0) {
            $qb->andWhere('f.typefichier <= 1');
        }
        else{
            $qb->andWhere('f.typefichier =:typefichier')
            ->setParameter('typefichier',$typefichier);
        }
        if ($idEdition=='na'){
            $edition=$session->get('edition');

        }else
        {
            $edition=$repositoryEdition->findBy(['id'=>$idEdition]);

        }
        if ($_REQUEST['menuIndex']==10){
            $qb->leftJoin('f.equipe','eq')
                ->andWhere('eq.selectionnee = TRUE')
                ->addOrderBy('eq.lettre','ASC');

        }
        if ($idEquipe!='na'){
            $equipe=$repositoryEquipe->findOneBy(['id'=>$idEquipe]);
            $edition=$equipe->getEdition();
            $qb->andWhere('f.equipe =:equipe')
                ->setParameter('equipe',$equipe)
                ->leftJoin('f.prof','p')
                ->andWhere('p.equipe =:equipeprof')
                ->setParameter('equipeprof',$equipe);
        }
        $qb->andWhere('f.edition =:edition')
        ->setParameter('edition',$edition);
        $fichiers=$qb->getQuery()->getResult();

        $zipFile = new \ZipArchive();
        $now = new \DateTime();
        $fileNameZip = 'telechargement_olymphys_' . $now->format('d-m-Y\-His');
        if (($zipFile->open($fileNameZip, ZipArchive::CREATE) === TRUE) and (null!==$fichiers)) {

            foreach ($fichiers as $fichier) {
                try {

                    $fileName=$this->getParameter('app.path.fichiers').'/'. $this->getParameter('type_fichier')[$typefichier].'/'.$fichier->getFichier();

                    $zipFile->addFromString(basename($fileName ), file_get_contents($fileName));//voir https://stackoverflow.com/questions/20268025/symfony2-create-and-download-zip-file

                } catch (\Exception $e) {

                }

            }

            $zipFile->close();


        }
            $response = new Response(file_get_contents($fileNameZip));//voir https://stackoverflow.com/questions/20268025/symfony2-create-and-download-zip-file

            $disposition = HeaderUtils::makeDisposition(
                HeaderUtils::DISPOSITION_ATTACHMENT,
                $fileNameZip
            );
            $response->headers->set('Content-Type', 'application/zip');
            $response->headers->set('Content-Disposition', $disposition);

            @unlink($fileNameZip);
            return $response;
        }



    public function configureFields(string $pageName): iterable

    {   $edition=$this->requestStack->getSession()->get('edition');
        $edition=$this->em->merge($edition);
        $numtypefichier=$_REQUEST['typefichier'];

        $concours=$_REQUEST['concours'];
        if ($pageName==Crud::PAGE_NEW){

            $panel1 = FormField::addPanel('<font color="red" > Déposer un nouveau '.$this->getParameter('type_fichier_lit')[$this->set_type_fichier($_REQUEST['menuIndex'],$_REQUEST['submenuIndex'])].'  </font> ');
            $numtypefichier=$this->set_type_fichier($_REQUEST['menuIndex'],$_REQUEST['submenuIndex']);

        }
        if ($pageName==Crud::PAGE_EDIT){

            $panel1 = FormField::addPanel('<font color="red" > Editer le fichier '.$this->getParameter('type_fichier_lit')[$this->set_type_fichier($_REQUEST['menuIndex'],$_REQUEST['submenuIndex'])].'  </font> ');
            $numtypefichier=$this->set_type_fichier($_REQUEST['menuIndex'],$_REQUEST['submenuIndex']);

        }

        $equipe = AssociationField::new('equipe') ->setFormTypeOptions(['data_class'=> null])
                    ->setQueryBuilder(function ($queryBuilder) {
                        $tag=substr(explode('?',explode('&',$_REQUEST['referrer'])[0])[1],-1);


                    return $queryBuilder->select()->andWhere('entity.edition =:edition')
                                        ->andWhere('entity.selectionnee =:selectionnee ')
                                        ->setParameter('edition',$this->requestStack->getSession()->get('edition'))
                                        ->setParameter('selectionnee',$tag)
                                        ->addOrderBy('entity.edition','DESC')
                                        ->addOrderBy('entity.lettre','ASC')
                                        ->addOrderBy('entity.numero','ASC'); }
                    );
        $fichierFile = Field::new('fichierFile','fichier')
            ->setFormType(VichFileType::class)
            ->setLabel('Fichier')
            ->onlyOnForms()
            ->setFormTypeOption('allow_delete',false);//sinon la case à cocher delete s'affiche
        //$numtypefichier=$this->set_type_fichier($_REQUEST['menuIndex'],$_REQUEST['submenuIndex']);
        switch ($numtypefichier){
            case 0 :$article= 'le';
                    break;
            case 1 :$article= 'l\'';
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
            case 7 :$article= 'le';
                break;
        }

        $panel2 = FormField::addPanel('<font color="red" > Modifier '.$article.' '.$this->getParameter('type_fichier_lit')[$this->set_type_fichier($_REQUEST['menuIndex'],$_REQUEST['submenuIndex'])] .'</font> ');
        $id = IntegerField::new('id', 'ID');
        $fichier = TextField::new('fichier')->setTemplatePath('bundles\\EasyAdminBundle\\liste_fichiers.html.twig');


        $typefichier = IntegerField::new('typefichier');
        if ($pageName==Crud::PAGE_INDEX){
            $context = $this->adminContextProvider->getContext();
            $context->getRequest()->query->set('concours',$_REQUEST['concours']);
            $context->getRequest()->query->set('typefichier',$_REQUEST['typefichier']);
        }
        $annexe= ChoiceField::new('typefichier','Mémoire ou annexe')
                ->setChoices(['Memoire'=>0,'Annexe'=>1])
                ->setFormTypeOptions(['required'=>true])
                ->setColumns('col-sm-4 col-lg-3 col-xxl-2') ;
        $national = BooleanField::new('national');
        $updatedAt = DateTimeField::new('updatedAt');
        $nomautorisation = TextField::new('nomautorisation');
        $edition = AssociationField::new('edition');
        $eleve = AssociationField::new('eleve')->setQueryBuilder(function ($queryBuilder) {
            return $queryBuilder->select()->leftJoin('entity.equipe','eq')
                                ->where('eq.edition =:edition')
                                ->setParameter('edition',$this->requestStack->getSession()->get('edition'))
                                ->addOrderBy('eq.numero','ASC')
                               ;}
        );
        $prof = AssociationField::new('prof')->setQueryBuilder(function ($queryBuilder) {
                $qb=$queryBuilder;

            return $queryBuilder->select()
                    ->leftJoin('entity.autorisationphotos','aut')
                    ->andWhere($qb->expr()->like('entity.roles',':roles'))
                    ->setParameter('roles','a:2:{i:0;s:9:"ROLE_PROF";i:1;s:9:"ROLE_USER";}')
                   // ->orWhere($qb->expr()->like('entity.roles',':roles'))
                   // ->setParameter('roles','%i:0;s:9:"ROLE_PROF";i:2;s:9:"ROLE_USER";%')
                    ->addOrderBy('entity.nom','ASC');

                    //    ->addOrderBy('entity.numero','ASC'))
                    ;});
        $editionEd = TextareaField::new('edition.ed');
        $equipeNumero = IntegerField::new('equipe.numero');
        $equipeLettre = TextareaField::new('equipe.lettre');
        $equipeCentreCentre = TextareaField::new('equipe.centre.centre');
        $equipeTitreprojet = TextareaField::new('equipe.titreprojet');
        $updatedat = DateTimeField::new('updatedat', 'Déposé le ');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$editionEd, $equipeNumero, $equipeLettre, $equipeTitreprojet, $fichier, $updatedat];
        }
        if (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $fichier, $typefichier, $national, $updatedAt, $nomautorisation, $edition, $equipe, $eleve, $prof];
        }
        if (Crud::PAGE_NEW === $pageName) {


            if ($numtypefichier==0)
            {  return [$panel1, $equipe, $fichierFile, $annexe];}
           if (($numtypefichier==2)or($numtypefichier==3)or($numtypefichier==4)or($numtypefichier==5)){
                return [$panel1, $equipe, $fichierFile];
            }
           if($numtypefichier==6){

                return [$panel1, $eleve,$prof,$fichierFile];
            }
        }
        if (Crud::PAGE_EDIT === $pageName) {
            if ($_REQUEST['typefichier']==0)
            {  return [$panel1, $equipe, $fichierFile, $annexe];}
            if (($_REQUEST['typefichier']==2)or($_REQUEST['typefichier']==3)or($_REQUEST['typefichier']==4)or($_REQUEST['typefichier']==5)){
                return [$panel1, $equipe, $fichierFile];
            }
           if($_REQUEST['typefichier']==6){

                return [$panel1, $equipe, $eleve,$prof,$fichierFile];
            }
            }

    }
    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $session = $this->requestStack->getSession();
        $context = $this->adminContextProvider->getContext();

        $repositoryEdition = $this->getDoctrine()->getManager()->getRepository('App:Edition');
        $repositoryCentrescia = $this->getDoctrine()->getManager()->getRepository('App:Centrescia');

        //$typefichier=$this->set_type_fichier($_REQUEST['menuIndex'],$_REQUEST['submenuIndex']);
        $typefichier = $context->getRequest()->query->get('typefichier');


        $concours = $context->getRequest()->query->get('concours');
        if ($concours == null) {
            $_REQUEST['menuIndex'] == 10 ? $concours = 1 : $concours =0;
        }
        if ($typefichier == 0) {
            $qb = $this->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters)
                ->andWhere('entity.typefichier <=:typefichier')
                ->setParameter('typefichier', $typefichier + 1);
        }
        if ($typefichier > 1) {
            $qb = $this->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters)
                ->andWhere('entity.typefichier =:typefichier')
                ->setParameter('typefichier', $typefichier);

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
                $idCentre = $context->getRequest()->query->get('filters')['centre']['value'];
                $centre = $repositoryCentrescia->findOneBy(['id' => $idCentre]);
                $session->set('titrecentre', $centre);

            }
            //$qb = $this->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters);
        }
        $qb->leftJoin('entity.equipe', 'e');
        if (($typefichier == 4) and ($concours == 1)) {//affiche uniquement les autorisations des équipes sélectionnées pour le choix du concours national
            $qb->andWhere('e.selectionnee = TRUE');
            }
        elseif($typefichier !=6) {//Les autorisations photos ne tiennent pas compte du caractère national du concours
            $qb->andWhere('entity.national =:concours')
                ->setParameter('concours', $concours);
        }


        if ($concours==0){
            $qb->addOrderBy('e.numero','ASC');}
        if ($concours==1) {
            $qb-> addOrderBy('e.lettre', 'ASC');
        }

        return $qb;
    }
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
            {   //Nécessaire pour que les fichiers déjà existants d'une équipe soient écrasés, non pas ajoutés

                $repositoryEdition=$this->getDoctrine()->getRepository(Edition::class);
                $session=$this->requestStack->getSession();
                $edition=$session->get('edition');
                $edition=$this->em->merge($edition);//nécessaire pour éviter que le programme ne considère $edition comme un nouvel objet edition
                $validator = new valid_fichiers($this->validator,$this->parameterBag,$this->requestStack);
                $dateconect = new \DateTime('now');
                $equipe = $entityInstance->getEquipe();
                $repositoryFichiers = $this->getDoctrine()->getManager()->getRepository('App:Fichiersequipes');

                $pos=strpos($_REQUEST['referrer'],'typefichier');
                $typefichier=substr($_REQUEST['referrer'],$pos+12,5);



                $entityInstance->setTypefichier($typefichier);


                $entityInstance->setEdition($edition);
                $ErrorMessage = $session->get('easymessage');
                if ($ErrorMessage != null) {
                    $this->addFlash('alert', $ErrorMessage);
                    //dd($ErrorMessage);

                    $this->redirectToRoute('admin', $_REQUEST);
                } else {
                    if ($typefichier!=6) {
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

                            parent::persistEntity($entityManager, $entityInstance);


                        }
                    }
                    if ($typefichier==6) {

                        $entityInstance->setNational(0);
                        $citoyen=$entityInstance->getProf();
                        $quidam='Prof';
                        if ($citoyen==null){
                            $citoyen=$entityInstance->getEleve();
                            $entityInstance->setEquipe($citoyen->getEquipe());
                            $quidam='Eleve';
                        }
                        $citoyen=$this->em->merge($citoyen);
                        $oldfichier = $repositoryFichiers->createQueryBuilder('f')
                            ->where('f.prof =:citoyen or f.eleve=:citoyen')
                            ->setParameter('citoyen', $citoyen)
                            ->andWhere('f.typefichier =:typefichier')
                            ->setParameter('typefichier', $entityInstance->getTypefichier())->getQuery()->getOneOrNUllResult();
                        $entityInstance->setNomautorisation($citoyen->getNom() . '-' . $citoyen->getPrenom());
                        if (null!=$oldfichier) {

                            $citoyen->setAutorisationphotos(null);
                            $this->em->persist($citoyen);

                            if ($quidam == 'Prof') {

                                $oldfichier->setProf(null);
                                $entityInstance->setProf($citoyen);
                                $entityInstance->setNomautorisation($citoyen->getNomPrenom());
                            } else {
                                $oldfichier->setEleve(null);
                                $entityInstance->setEleve($citoyen);
                                $entityInstance->setNomautorisation($citoyen->getNomPrenom());
                            }

                            $this->em->remove($oldfichier);
                            $this->em->flush();
                            $citoyen->setAutorisationphotos($entityInstance);
                            $this->em->persist($citoyen);
                            $this->em->flush();

                        }




                        parent::persistEntity($entityManager, $entityInstance);

                    }

                }
            }
        public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
                {   //Nécessaire pour que les fichiers déjà existants d'une équipe soient écrasés, non pas ajoutés
                    //$validator = new valid_fichiers($this->validator, );
                    $session=$this->requestStack->getSession();
                    $dateconect = new \DateTime('now');
                    $equipe = $entityInstance->getEquipe();
                    $repositoryFichiers = $this->getDoctrine()->getManager()->getRepository('App:Fichiersequipes');
                    $ErrorMessage = $session->get('messageeasy');

                    if ($ErrorMessage['text'] != '') {
                        //$this->flashbag=$ErrorMessage['text'];
                       $this->flashbag->addAlert($ErrorMessage['text']);

                        //admin?crudAction=edit&crudControllerFqcn=App\Controller\Admin\FichiersequipesCrudController&entityId=462&menuIndex=8&referrer=https%3A%2F%2Flocalhost%3A8000%2Fadmin%3Fconcours%3D0%26crudAction%3Dindex%26crudControllerFqcn%3DApp%255CController%255CAdmin%255CFichiersequipesCrudController%26entityFqcn%3DApp%255CEntity%255CFichiersequipes%26menuIndex%3D8%26signature%3DT3WMMc32cNzYTmj2VTovHaw-6_5aoMMGxDNaojh1Oig%26submenuIndex%3D1%26typefichier%3D0&signature=t466dtQyEuhdvw3Dht9OwhQxodEAsmqJiympg4pECwA&submenuIndex=1
                        //dd($this->requestStack);
                      $session->set('messageeasy',['text'=>'']);
                       $context=$this->adminContextProvider->getContext();
                       $response =new Response();
                       $this->redirectAfterError($context);
                        //dd($_REQUEST);
                        // $this->redirectToRoute('app_admin_dashboard_index',['crudController'=>'FichiersequipesCrudController','crudAction'=>'edit','entityId'=>$entityInstance->getId()]);
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
        public function redirectAfterError($context){
            $url = $this->get(AdminUrlGenerator::class)
                ->setAction(Action::EDIT)
                ->setEntityId($context->getEntity()->getPrimaryKeyValue())
                ->generateUrl();

            return $this->redirect($url);
        }


}
