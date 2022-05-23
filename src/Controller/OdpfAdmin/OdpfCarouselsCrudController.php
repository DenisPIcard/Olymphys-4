<?php

namespace App\Controller\OdpfAdmin;

use App\Entity\Odpf\OdpfArticle;
use App\Entity\Odpf\OdpfCarousels;
use App\Entity\Odpf\OdpfImagescarousels;
use App\Form\OdpfChargeDiapoType;
use App\Form\OdpfImagesType;
use App\Service\ImagesCreateThumbs;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Provider\AdminContextProvider;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class OdpfCarouselsCrudController extends AbstractCrudController
{

    private ManagerRegistry $doctrine;
    private AdminContextProvider $context;
    private $adminUrlGenerator;


    public function __construct(ManagerRegistry $doctrine, AdminContextProvider $adminContextProvider,AdminUrlGenerator $adminUrlGenerator)
    {
        $this->doctrine = $doctrine;
        $this->context = $adminContextProvider;
        $this->adminUrlGenerator=$adminUrlGenerator;
    }

    public static function getEntityFqcn(): string
    {
        return OdpfCarousels::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return Crud::new()->setFormThemes(['bundles/EasyAdminBundle/odpf/odpf_form_images_carousels.html.twig', '@EasyAdmin/crud/form_theme.html.twig'])
            ->overrideTemplate('crud/edit', 'bundles/EasyAdminBundle/crud/edit.html.twig');


    }

    public function configureFields(string $pageName): iterable
    {
        if ($pageName == 'edit') {

            $carousel = $this->doctrine->getRepository(OdpfCarousels::class)->find($_REQUEST['entityId']);
            $listeImages = $this->doctrine->getRepository(OdpfImagescarousels::class)->findBy(['carousel' => $carousel]);
        }
        $name = TextField::new('name', 'nom');
        $images = CollectionField::new('images')->setEntryType(OdpfImagesType::class)
            ->setFormTypeOptions(['block_name' => 'image', 'allow_add' => true, 'prototype' => true])
            ->setEntryIsComplex(true)
            ->renderExpanded(false);

        $updatedAt = DateTimeField::new('updatedAt');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$name, $images, $updatedAt];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$name, $images, $updatedAt];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$name, $images];
        } elseif (Crud::PAGE_EDIT === $pageName) {

            return [$name, $images];
        }


    }


    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
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

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->doctrine->getManager()->persist($entityInstance);
        $this->doctrine->getManager()->flush();
        $images = $entityInstance->getImages();
        foreach ($images as $image) {
            if (file_exists('odpf-images/imagescarousels/'.$image->getName())) {
                $imagesCreateThumbs = new ImagesCreateThumbs();
                $imagesCreateThumbs->createThumbs($image);
            }
        }
        parent::persistEntity($entityManager, $entityInstance); // TODO: Change the autogenerated stub
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $idCarousel=$entityInstance->getId();
        $carouselOrigine=$this->doctrine->getRepository(OdpfCarousels::class)->findOneBy(['id'=>$idCarousel]);
        $images = $entityInstance->getImages();

        $imagesOrigine=$carouselOrigine->getImages();

        $i = 0;
        $imagesRemoved = null;
        foreach ($images as $image) {

            if ($image->getImagefile()->getPath() == '/tmp') {

                $imagesRemoved[$i] = $image->getName();
                $i = +1;
            }
        }
        $this->doctrine->getManager()->persist($entityInstance);
        $this->doctrine->getManager()->flush();
        $imagesCreateThumbs = new ImagesCreateThumbs();
        foreach ($images as $image) {
            if (file_exists('odpf-images/imagescarousels/'.$image->getName())) {
                $imagesCreateThumbs->createThumbs($image);
            }
        }
        if ($imagesRemoved !== null) {   //pour effacer les images intiales après leur remplacement dans le carousel
            foreach ($imagesRemoved as $imageRemoved) {
                if ($imageRemoved !== null) {
                    if (file_exists($this->getParameter('app.path.imagescarousels') . '/' . $imageRemoved)) {
                        unlink($this->getParameter('app.path.imagescarousels') . '/' . $imageRemoved);
                    }
                }
            }
        }
    }

    /**
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @Route("/admin/OdpfCaroussels,{idCarousel}", name="add_diapo")
     *
     */
    public function addDiapo(Request $request, $idCarousel)
    {

        $carousel = $this->doctrine->getRepository(OdpfCarousels::class)->findOneBy(['id' => $idCarousel]);
        $url = $this->adminUrlGenerator
            ->setController(OdpfCarouselsCrudController::class)
            ->setAction('edit')
            ->setEntityId($idCarousel)
            ->setDashboard(OdpfDashboardController::class)
            ->generateUrl();
        $diapo = new OdpfImagescarousels();
        $diapo->setCarousel($carousel);
        $form = $this->createForm(OdpfChargeDiapoType::class, $diapo);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->doctrine->getManager();

            $em->persist($diapo);
            $em->flush();
            $thumbscreate = new ImagesCreateThumbs();
            $thumbscreate->createThumbs($diapo);
            return new RedirectResponse($url);


        }

        return $this->render('OdpfAdmin/charge-diapo.html.twig', ['form' => $form->createView(), 'idCarousel' => $idCarousel, 'url' => $url]);
    }


}
