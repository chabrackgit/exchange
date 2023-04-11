<?php

namespace App\Controller;

use DateTime;
use DateTimeImmutable;
use App\Entity\Fichier;
use phpseclib3\Net\SFTP;
use App\Service\Sftp\SftpService;
use App\Repository\FichierRepository;
use App\Service\Kyriba\KyribaService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ExtractController extends AbstractController
{

    private $kyribaService;
    private $stfpService;

    public function __construct(
        KyribaService $kyribaService, 
        SftpService $sftpService) {
        $this->kyribaService = $kyribaService;
        $this->stfpService = $sftpService;
    }

    #[Route('/exportKyribaToPs', name: 'app_export_ktoPs')]
    public function exportPs(): Response
    {
        $connexionKyriba = $this->stfpService->ConnexionSftp($_ENV['HOSTKYRIBA'], $_ENV['PORTKYRIBA'], $_ENV['LOGINKYRIBA'], $_ENV['PWDKYRIBA']);
        $connexionPs = $this->stfpService->ConnexionSftp($_ENV['HOSTPS'], $_ENV['PORTPS'], $_ENV['LOGINPS'], $_ENV['PWDPS']);
        $this->kyribaService->KyribaToPs($connexionKyriba, $connexionPs);
        return $this->redirectToRoute('admin');
    }

    #[Route('/exportKyribaToUbw', name: 'app_export_ktoUbw')]
    public function exportUbw(): Response
    {
        $connexionKyriba = $this->stfpService->ConnexionSftp($_ENV['HOSTKYRIBA'], $_ENV['PORTKYRIBA'], $_ENV['LOGINKYRIBA'], $_ENV['PWDKYRIBA']);
        $connexionUbw = $this->stfpService->ConnexionSftp($_ENV['HOSTUBW'], $_ENV['PORTUBW'], $_ENV['LOGINUBW'], $_ENV['PWDUBW']);
        $this->kyribaService->KyribaToUbw($connexionKyriba, $connexionUbw);
        return $this->redirectToRoute('admin');
    }

    

    #[Route('/traitement', name: 'traitement')]
    public function executeTraitement(EntityManagerInterface $em): Response
    {   
        $connexionKyriba = $this->stfpService->ConnexionSftp($_ENV['HOSTKYRIBA'], $_ENV['PORTKYRIBA'], $_ENV['LOGINKYRIBA'], $_ENV['PWDKYRIBA']);
        $connexionUbw = $this->stfpService->ConnexionSftp($_ENV['HOSTUBW'], $_ENV['PORTUBW'], $_ENV['LOGINUBW'], $_ENV['PWDUBW']);
        $connexionPs = $this->stfpService->ConnexionSftp($_ENV['HOSTPS'], $_ENV['PORTPS'], $_ENV['LOGINPS'], $_ENV['PWDPS']);
        $retour = $this->kyribaService->ExportKyriba($connexionKyriba, $connexionUbw, $connexionPs);

        dd('fini traitement');

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'ExtractController',
        ]);
    }

    #[Route('/test', name: 'test')]
    public function test(EntityManagerInterface $em): Response
    {   
        $filename = 'INSEEC.NC4.EXPORT.2023032213263448397.COMP.00941_PS.CA.txt';

        

        return $this->render('admin/test.html.twig', [
            'filename' => $filename,
        ]);
    }
   
}
