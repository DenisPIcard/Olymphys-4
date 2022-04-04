<?php

namespace App\Controller\OdpfAdmin;

use App\Entity\OdpfArticle;
use App\Entity\OdpfCarousels;
use App\Entity\Photos;
use App\Form\OdpfImagesType;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use FM\ElfinderBundle\Form\Type\ElFinderType;

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


    public function configureFields(string $pageName): iterable
    {
        return [
            $name = TextField::new('name', 'nom'),
            $images = CollectionField::new('images')->setEntryType(OdpfImagesType::class)->setTemplatePath('bundles/EasyAdminBundle/odpf/odpf_images_carousels.html.twig'),
            $photos = CollectionField::new('photos')->setFormType(ElFinderType::class)->setFieldFqcn(Photos::class)->setFormTypeOptions(['mapped' => false])->setTemplatePath('bundles/EasyAdminBundle/odpf/odpf_images_carousels.html.twig'),
        ];
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




}
