<?php

namespace App\Controller\Admin;

use App\Entity\Entite;
use App\Entity\Etablissement;
use App\Entity\Fichier;
use App\Entity\Session;
use App\Entity\TemplateCode;
use App\Entity\TypeTransfert;
use App\Repository\FichierRepository;
use App\Repository\SessionRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class DashboardController extends AbstractDashboardController
{

    private $fichierRepository;
    private $sessionRepository;


    public function __construct(
        FichierRepository $fichierRepository,
        SessionRepository $sessionRepository
        )
    {
        $this->fichierRepository = $fichierRepository;
        $this->sessionRepository = $sessionRepository;
    }

    #[Route('/admin3fza38fv', name: 'admin')]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        $sessionExport = $this->sessionRepository->findOneBy(['code' => Fichier::SESSION_EXPORT]);
        $sessionReport = $this->sessionRepository->findOneBy(['code' => Fichier::SESSION_REPORT]);
        $fichiersExport = $this->fichierRepository->findBy(['session' => $sessionExport]);
        $fichiersReport = $this->fichierRepository->findBy(['session' => $sessionReport]);
        $countTotal = count($fichiersExport) + count($fichiersReport);
        //return $this->render('bundles/EasyAdminBundle/welcome.html.twig');
        return $this->render('admin/dashboard.html.twig', [
            'countTotal' => $countTotal,
            'countExport' => count($fichiersExport),
            'countReport' => count($fichiersReport),
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Exchange Kyriba');
    }

    public function configureMenuItems(): iterable
    {
        if ($this->isGranted('ROLE_TECHNICIEN') || $this->isGranted('ROLE_ADMIN')) {
            yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
            yield MenuItem::linkToCrud('Transfert Export', 'fas fa-arrow-down', Fichier::class);
            yield MenuItem::linkToCrud('Entite', 'fas fa-school', Entite::class);
            yield MenuItem::linkToCrud('Template code', 'fas fa-money-bill-trend-up', TemplateCode::class);
            yield MenuItem::linkToCrud('Type (IMPORT/EXPORT)', 'fas fa-solid fa-money-bill-transfer', TypeTransfert::class);
            yield MenuItem::linkToCrud('Session', 'fas fa-rectangle-list', Session::class);
            yield MenuItem::linkToCrud('Etablissement', 'fas fa-building-columns', Etablissement::class);
        } else {
            yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
            yield MenuItem::linkToCrud('Transfert Export', 'fas fa-arrow-down', Fichier::class);
        }
        
    }
}
