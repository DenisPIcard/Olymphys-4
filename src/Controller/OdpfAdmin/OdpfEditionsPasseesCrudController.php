<?php

namespace App\Controller\OdpfAdmin;

use App\Controller\Admin\AdminsiteCrudController;
use App\Entity\Odpf\OdpfEditionsPassees;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\FileUploadType;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Provider\AdminContextProvider;
use phpDocumentor\Reflection\PseudoTypes\False_;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RequestStack;
use Vich\UploaderBundle\Form\Type\VichFileType;


class OdpfEditionsPasseesCrudController extends AbstractCrudController
{
    private ManagerRegistry $doctrine;
    private RequestStack $requestStack;
    private AdminContextProvider $admincontext;

    function __construct(ManagerRegistry $doctrine,RequestStack $requestStack,AdminContextProvider $adminContext)
        {
            $this->doctrine= $doctrine;
            $this->requestStack=$requestStack;
            $this->admincontext=$adminContext;
        }
    public static function getEntityFqcn(): string
    {
        return OdpfEditionsPassees::class;
    }
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['edition' => 'DESC']);

           // ->overrideTemplate('crud/field/photoParrain', 'bundles/EasyAdminBundle/odpf/odpf-photoParrain.html.twig');

    }
    public function configureFields(string $pageName): iterable
    {   $photoParrain = TextField::new('photoParrain');
        $affiche = TextField::new('affiche');//->setTemplatePath( 'bundles/EasyAdminBundle/odpf/odpf-affiche.html.twig');;

        if (Crud::PAGE_EDIT === $pageName) {
            $idEdition = $_REQUEST['entityId'];
            $edition = $this->doctrine->getRepository('App:Odpf\OdpfEditionsPassees')->findOneBy(['id' => $idEdition]);
            $photoParrain = ImageField::new('photoParrain')->setUploadDir('public/odpf-archives/' . $edition->getEdition() . '/parrain');
            $photoParrain = ImageField::new('photoParrain')->setUploadDir('public/odpf-archives/' . $edition->getEdition() . '/affiche');
            $photoFile = Field::new('photoParrain', 'Photo du parrain')
                ->setFormType(FileUploadType::class)
                ->setLabel('Photo du parrain')
                ->onlyOnForms()
                ->setFormTypeOptions(['data_class'=>null, 'upload_dir'=>$this->getParameter('app.path.odpf_archives').'/'.$edition->getEdition().'/parrain']);

            $afficheFile = Field::new('affiche', 'Affiche')
                ->setFormType(FileUploadType::class)
                ->setLabel('Affiche')
                ->onlyOnForms()
                ->setFormTypeOptions(['data_class'=>null, 'upload_dir'=>$this->getParameter('app.path.odpf_archives').'/'.$edition->getEdition().'/affiche']);

        }

        $id=IntegerField::new('id');
        $edition = TextField::new('edition');
        $pseudo = TextField::new('pseudo');
        $lieu = TextField::new('lieu');
        $annee = TextField::new('annee');
        $ville = TextField::new('ville');
        $datecia = TextField::new('dateCia');
        $datecn = TextField::new('dateCn');
        $dateinscription = TextField::new('dateinscription');
        $nomParrain = TextField::new('nomParrain');
        $titreParrain = TextField::new('titreParrain');


        if (Crud::PAGE_INDEX === $pageName) {
            return [$edition, $pseudo, $annee, $lieu, $ville, $datecia, $datecn];
        }
        if (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $edition, $pseudo, $annee, $lieu, $ville, $datecia, $datecn, $dateinscription, $nomParrain, $titreParrain, $photoParrain, $affiche];
        }
        /*if (Crud::PAGE_NEW === $pageName) {
            return [$edition, $pseudo, $annee, $lieu, $ville, $datecia, $datecn, $dateinscription, $nomParrain, $titreParrain, $photoFiLe, $afficheFile];

        }*/
        if (Crud::PAGE_EDIT === $pageName) {
            return [$edition, $pseudo, $annee, $lieu, $ville, $datecia, $datecn, $dateinscription, $nomParrain, $titreParrain, $photoFile, $afficheFile];

        }

        return parent::configureFields($pageName); // TODO: Change the autogenerated stub
        }
    public function configureActions(Actions $actions): Actions
    {
        $actions = $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->add(Crud::PAGE_EDIT, Action::INDEX, 'Retour à la liste')
            ;
        return $actions;
    }
}
