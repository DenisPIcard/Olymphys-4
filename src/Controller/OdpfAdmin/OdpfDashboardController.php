<?php

namespace App\Controller\OdpfAdmin;


use App\Entity\Odpf\OdpfArticle;
use App\Entity\Odpf\OdpfCarousels;
use App\Entity\Odpf\OdpfCategorie;
use App\Entity\Odpf\OdpfDocuments;
use App\Entity\Odpf\OdpfEditionsPassees;
//use App\Entity\Odpf\OdpfFaq;
use App\Entity\Odpf\OdpfLogos;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OdpfDashboardController extends AbstractDashboardController
{
    /**
     * @Route("/odpfadmin", name="odpfadmin")
     */
    public function index(): Response
    {
        return $this->render('bundles/EasyAdminBundle/odpf/message_accueil.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('<img src="https://upload.wikimedia.org/wikipedia/commons/3/36/Logo_odpf_long.png" alt="logo des OdpF"  width="160"/>');
    }

    public function configureCrud(): Crud
    {
        return Crud::new()
            ->setDateFormat('dd/MM/yyyy')
            ->setDateTimeFormat('dd/MM/yyyy HH:mm:ss')
            ->setTimeFormat('HH:mm');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
        //yield MenuItem::linkToCrud('Foire aux Questions','fas fa-list', OdpfFaq::class);
        yield MenuItem::linkToCrud('Articles', 'fas fa-list', OdpfArticle::class);
        yield MenuItem::linkToCrud('Les éditions passées', 'fas fa-list', OdpfEditionsPassees::class);
        yield MenuItem::linkToCrud('Categories', 'fas fa-list', OdpfCategorie::class);
        yield MenuItem::linkToCrud('Documents du site', 'fas fa-book', OdpfDocuments::class);
        yield MenuItem::linkToCrud('Logos du site', 'fas fa-book', OdpfLogos::class);
        yield MenuItem::linkToCrud('OdpfCarousels', 'fas fa-list', OdpfCarousels::class);
        yield MenuItem::linktoRoute('Retour à la page d\'accueil', 'fas fa-home', 'core_home');
        yield MenuItem::linkToLogout('Déconnexion', 'fas fa-door-open');
    }


}
