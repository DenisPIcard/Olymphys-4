<?php

namespace App\Controller\OdpfAdmin;

use App\Entity\Odpf\OdpfArticle;
use App\Entity\Odpf\OdpfCarousels;
use App\Form\OdpfImagesType;
use App\Form\OdpfFormImagesType;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\FileUploadType;
use FM\ElfinderBundle\Form\Type\ElFinderType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Vich\UploaderBundle\Mapping\Annotation\UploadableField;


class OdpfCarouselsCrudController extends AbstractCrudController
{

    private ManagerRegistry  $doctrine;

    public function __construct( ManagerRegistry $doctrine)
    {
        $this->doctrine=$doctrine;
    }

    public static function getEntityFqcn(): string
    {
        return OdpfCarousels::class;
    }
    public function configureCrud(Crud $crud): Crud
    {
        return Crud::new()
            // ...

            // the first argument is the "template name", which is the same as the
            // Twig path but without the `@EasyAdmin/` prefix
            //->overrideTemplate('label/null', 'admin/labels/my_null_label.html.twig')

            ->overrideTemplates([
              //  'crud/edit' => 'bundles/EasyAdminBundle/edit.html.twig',
                'crud/field/collection' => 'bundles/EasyAdminBundle/collection.html.twig',
            ])
            ;
    }

    public function configureFields(string $pageName): iterable
    {    $listeImages=[];
        if ($pageName == 'edit') {

            $listeImages = $this->doctrine->getRepository('App:Odpf\OdpfCarousels')->findOneBy(['id' => $_REQUEST['entityId']])->getImages();
            $i=0;
            foreach($listeImages as $image) {
                $imageedit[$i] = Field::new('imageFile');//->setEntryType(OdpfFormImagesType::class);
                $i =$i+1;
            }

        }
            $name = TextField::new('name', 'nom');
            $images = CollectionField::new('images')->setEntryType(OdpfImagesType::class)->setTemplatePath('bundles/EasyAdminBundle/odpf/odpf_images_carousels.html.twig');
            $updatedAt=DateTimeField::new('updatedAt');
            // $photos = CollectionField::new('photos')->setFormType(ElFinderType::class)->setFieldFqcn(Photos::class)->setFormTypeOptions(['mapped' => false])->setTemplatePath('bundles/EasyAdminBundle/odpf/odpf_images_carousels.html.twig'),

        if (Crud::PAGE_INDEX === $pageName) {
            return [$name, $images, ];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$name, $images,$updatedAt];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$name, $images];
        } elseif (Crud::PAGE_EDIT === $pageName) {

            return [$name,$imageedit];
        }


    }
    public function configureActions(Actions $actions): Actions
    {

        $modifierCarousel = Action::new('Modifier', 'Modifier', 'fa fa-pencil')
            ->linkToCrudAction('modifier');//->createAsBatchAction();
        return $actions->add(Crud::PAGE_INDEX, $modifierCarousel);


    }

    public function deleteEntity(EntityManagerInterface $entityManager,  $entityInstance): void
    {
        $repositoryArticle = $this->doctrine->getRepository(OdpfArticle::class);
        $em = $this->doctrine->getManager();;

        $articles = $repositoryArticle->findBy(['carousel' => $entityInstance]);
        foreach ($articles as $article) {
            $article->setCarousel(null);
            $em->persist($article);
            $em->flush();
        }
        $images = $entityInstance->getImages();

        foreach ($images as $image) {


            $entityInstance->removeImage($image);


        }
        parent::deleteEntity($entityManager, $entityInstance); // TODO: Change the autogenerated stub
    }



    /**
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @Route("/admin/OdpfCarouselCrud", name="modif_carousel")
     *
     */
    public function modifier(Request $request, ?AdminContext $context)
    {
        $id = $context->getRequest()->query->get('entityId');

        $repositoryCarousel=$this->doctrine->getRepository(OdpfCarousels::class);
        $carousel=$repositoryCarousel->findOneBy(['id'=>$id]);;

        $images=$carousel->getImages();

        $i=1;


        return $this->render('OdpfAdmin/modifcarousel.html.twig',array('carousel'=>$carousel,'images'=>$images));
    }


}
