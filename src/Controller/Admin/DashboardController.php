<?php

namespace App\Controller\Admin;

use App\Entity\Cadeaux;
use App\Entity\Centrescia;
use App\Entity\Classement;
use App\Entity\Docequipes;
use App\Entity\Edition;
use App\Entity\Elevesinter;
use App\Entity\Equipes;
use App\Entity\Equipesadmin;
use App\Entity\Fichiersequipes;
use App\Entity\Jures;
use App\Entity\Photos;
use App\Entity\Prix;
use App\Entity\User;
use App\Entity\Videosequipes;
use App\Entity\Visites;
use App\Entity\Professeurs;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('<img src="https://upload.wikimedia.org/wikipedia/commons/3/36/Logo_odpf_long.png"" alt="logo des OdpF"  width="160"/>');
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
        $submenu1 = [
            MenuItem::linkToCrud('Centres interacadémiques', 'fas fa-city', Centrescia::class),

            MenuItem::linkToCrud('Les mémoires', 'fas fa-book', Fichiersequipes::class),
            MenuItem::linkToCrud('Les résumés', 'fas fa-book', Fichiersequipes::class),
            MenuItem::linkToCrud('Les fiches sécurités', 'fas fa-book', Fichiersequipes::class),
            MenuItem::linkToCrud('Les diaporamas', 'fas fa-book', Fichiersequipes::class),
            MenuItem::linkToCrud('Les vidéos des équipes', 'fas fa-film', Videosequipes::class),

            MenuItem::linkToCrud(' Les autorisations photos', 'fas fa-balance-scale', Fichiersequipes::class),
            MenuItem::linkToCrud(' Les photos', 'fas fa-images', Photos::class),
            MenuItem::linkToCrud(' Les fichiers', 'fas fa-book', Fichiersequipes::class),
        ];

        $submenu2 = [
            MenuItem::section('Equipes'),
            MenuItem::linkToCrud('Palmares des équipes', 'fas fa-asterisk', Equipes::class),
            MenuItem::linkToCrud('Administration des équipes', 'fas fa-user-friends', Equipes::class),
            MenuItem::linkToCrud('Les mémoires', 'fas fa-book', Fichiersequipes::class),
            MenuItem::linkToCrud('Les résumés', 'fas fa-book', Fichiersequipes::class),
            MenuItem::linkToCrud('Les diaporamas', 'fas fa-book', Fichiersequipes::class),
            MenuItem::linkToCrud('Les présentations', 'fas fa-book', Fichiersequipes::class),
            MenuItem::linkToCrud('Les vidéos des équipes', 'fas fa-film', Videosequipes::class),
            MenuItem::linkToCrud('Les photos', 'fas fa-images', Photos::class),
            MenuItem::section('Les recompenses')->setPermission('ROLE_SUPER_ADMIN'),
            MenuItem::linkToCrud('Répartition des prix', 'fas fa-asterisk', Classement::class)->setPermission('ROLE_SUPER_ADMIN'),
            MenuItem::linkToCrud('Les Prix', 'fas fa-asterisk', Prix::class)->setPermission('ROLE_SUPER_ADMIN'),
            MenuItem::linkToCrud('Les Visites', 'fas fa-asterisk', Visites::class)->setPermission('ROLE_SUPER_ADMIN'),
            MenuItem::linkToCrud('Cadeaux', 'fas fa-asterisk', Cadeaux::class)->setPermission('ROLE_SUPER_ADMIN'),
        ];

        yield MenuItem::linkToCrud('Adminsite', 'fas fa-cogs', Edition::class)->setPermission('ROLE_SUPER_ADMIN');
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-user', User::class)->setPermission('ROLE_SUPER_ADMIN');
        yield MenuItem::linkToCrud('Affectation des jurés', 'fas fa-graduation-cap', Jures::class)->setPermission('ROLE_SUPER_ADMIN');
        yield MenuItem::linkToCrud('Documents à télécharger', 'fas fa-book', Docequipes::class);
        yield MenuItem::linkToCrud('Equipes inscrites', 'fas fa-user-friends', Equipesadmin::class);
        yield MenuItem::linkToCrud('Elèves inscrits', 'fas fa-child', Elevesinter::class);
        yield MenuItem::linkToCrud('Professeurs', 'fas fa-user-tie', Professeurs::class);
        yield MenuItem::subMenu('Concours interacadémique')->setSubItems($submenu1)->setCssClass('text-bold');
        yield MenuItem::subMenu('Concours national')->setSubItems($submenu2);
        yield MenuItem::linktoRoute('Retour à la page d\'accueil', 'fas fa-home', 'core_home');
        yield MenuItem::linktoRoute('Secrétariat du jury', 'fas fa-pencil-alt', 'secretariatjury_accueil')->setPermission('ROLE_SUPER_ADMIN');
        yield MenuItem::linktoRoute('Deconnexion', 'fas fa-door-open', 'logout');
    }
}
