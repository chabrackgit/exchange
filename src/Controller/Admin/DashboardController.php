<?php

namespace App\Controller\Admin;

use App\Entity\Entite;
use App\Entity\Etablissement;
use App\Entity\Fichier;
use App\Entity\Session;
use App\Entity\TemplateCode;
use App\Entity\TypeTransfert;
use App\Repository\FichierRepository;
use App\Repository\TemplateCodeRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class DashboardController extends AbstractDashboardController
{

    private $fichierRepository;
    private $templateCodeRepository;


    public function __construct(
        FichierRepository $fichierRepository,
        TemplateCodeRepository $templateCodeRepository
        )
    {
        $this->fichierRepository = $fichierRepository;
        $this->templateCodeRepository = $templateCodeRepository;
    }

    #[Route('/admin3fza38fv', name: 'admin')]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        $sessionExportUbw = $this->templateCodeRepository->findOneBy(['code' => 'RDCV']);
        $sessionExportPs = $this->templateCodeRepository->findOneBy(['code' => 'PS']);
        $sessionImportUbw = $this->templateCodeRepository->findOneBy(['code' => 'SDD']);
        $sessionImportPs = $this->templateCodeRepository->findOneBy(['code' => 'PS_SCT']);
        $fichiersExportUbw = $this->fichierRepository->findBy(['templateCode' => $sessionExportUbw]);
        $fichiersExportPs = $this->fichierRepository->findBy(['templateCode' => $sessionExportPs]);
        $countTotalExport = count($fichiersExportUbw) + count($fichiersExportPs);
        $fichiersImportUbw = $this->fichierRepository->findBy(['templateCode' => $sessionImportUbw]);
        $fichiersImportPs = $this->fichierRepository->findBy(['templateCode' => $sessionImportPs]);
        $countTotalImport = count($fichiersImportUbw) + count($fichiersImportPs);
        //return $this->render('bundles/EasyAdminBundle/welcome.html.twig');
        return $this->render('admin/dashboard.html.twig', [
            'countTotalExport' => $countTotalExport,
            'countTotalImport' => $countTotalImport,
            'countExportUbw' => count($fichiersExportUbw),
            'countExportPs' => count($fichiersExportPs),
            'countImportUbw' => count($fichiersImportUbw),
            'countImportPs' => count($fichiersImportPs),
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
            yield MenuItem::linkToCrud('Liste des transferts', 'fas fa-arrow-right-arrow-left', Fichier::class);
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
