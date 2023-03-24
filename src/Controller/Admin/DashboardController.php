<?php

namespace App\Controller\Admin;

use App\Entity\Entite;
use App\Entity\Source;
use App\Entity\Fichier;
use App\Entity\FichierImport;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Exchange Kyriba');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Export Kyriba', 'fas fa-list', Fichier::class);
        yield MenuItem::linkToCrud('Import', 'fas fa-list', FichierImport::class);
        yield MenuItem::linkToCrud('Entites', 'fas fa-list', Entite::class);
        yield MenuItem::linkToCrud('Sources', 'fas fa-list', Source::class);
    }
}
