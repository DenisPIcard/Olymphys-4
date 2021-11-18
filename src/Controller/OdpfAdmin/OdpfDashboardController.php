<?php

namespace App\Controller\OdpfAdmin;

use App\Entity\OdpfArticle;
use App\Entity\OdpfCategorie;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OdpfDashboardController extends AbstractDashboardController
{
    /**
     * @Route("/odpf_admin", name="odpfadmin")
     */
    public function index(): Response
    {
        return $this->render('OdpfAdmin/message_accueil.html.twig');
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
        yield MenuItem::linkToCrud('Articles', 'fas fa-list', OdpfArticle::class);
        yield MenuItem::linkToCrud('Articles', 'fas fa-list', OdpfCategorie::class);
    }


}
