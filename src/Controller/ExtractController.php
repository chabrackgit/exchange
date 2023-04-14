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
        $retour = $this->kyribaService->KyribaToPs($connexionKyriba, $connexionPs);
        if (array($retour) && !is_null($retour)) {
            foreach($retour as $arr) {
                $this->addFlash('danger', 'Transfert échoué : le fichier  '.$arr[0]->getNom()).' existe déjà';
            }
        } else {
            $this->addFlash('success', 'Transfert réussi : OK');
        }

        return $this->redirectToRoute('admin');
    }

    #[Route('/exportKyribaToUbw', name: 'app_export_ktoUbw')]
    public function exportUbw(): Response
    {
        $connexionKyriba = $this->stfpService->ConnexionSftp($_ENV['HOSTKYRIBA'], $_ENV['PORTKYRIBA'], $_ENV['LOGINKYRIBA'], $_ENV['PWDKYRIBA']);
        $connexionUbw = $this->stfpService->ConnexionSftp($_ENV['HOSTUBW'], $_ENV['PORTUBW'], $_ENV['LOGINUBW'], $_ENV['PWDUBW']);
        $retour = $this->kyribaService->KyribaToUbw($connexionKyriba, $connexionUbw);
        if (array($retour) && !is_null($retour)) {
            foreach($retour as $arr) {
                $this->addFlash('danger', 'Transfert échoué : le fichier  '.$arr[0]->getNom()).' existe déjà';
            }
        } else {
            $this->addFlash('success', 'Transfert réussi : OK');
        }

        return $this->redirectToRoute('admin');
    }

    #[Route('/importPsPayment', name: 'app_import_ps_payment')]
    public function importPsPayment(): Response
    {
        $connexionKyriba = $this->stfpService->ConnexionSftp($_ENV['HOSTKYRIBA'], $_ENV['PORTKYRIBA'], $_ENV['LOGINKYRIBA'], $_ENV['PWDKYRIBA']);
        $connexionPs = $this->stfpService->ConnexionSftp($_ENV['HOSTPS'], $_ENV['PORTPS'], $_ENV['LOGINPS'], $_ENV['PWDPS']);
        $retour = $this->kyribaService->ImportPsPayment($connexionKyriba, $connexionPs);
        if (array($retour) && !is_null($retour)) {
            foreach($retour as $arr) {
                $this->addFlash('danger', 'Transfert échoué : le fichier  '.$arr[0]->getNom()).' existe déjà';
            }
        } else {
            $this->addFlash('success', 'Transfert réussi : OK');
        }

        return $this->redirectToRoute('admin');
    }

    #[Route('/report', name: 'app_report')]
    public function reportKyribaToK(): Response
    {
        $connexionKyriba = $this->stfpService->ConnexionSftp($_ENV['HOSTKYRIBA'], $_ENV['PORTKYRIBA'], $_ENV['LOGINKYRIBA'], $_ENV['PWDKYRIBA']);
        $connexionPs = $this->stfpService->ConnexionSftp($_ENV['HOSTPS'], $_ENV['PORTPS'], $_ENV['LOGINPS'], $_ENV['PWDPS']);
        $retour = $this->kyribaService->report($connexionKyriba, $connexionPs);

        if (array($retour) && !is_null($retour)) {
            foreach($retour as $arr) {
                $this->addFlash('danger', 'Transfert échoué : le fichier  '.$arr[0]->getNom()).' existe déjà';
            }
        } else {
            $this->addFlash('success', 'Transfert réussi : OK');
        }
        
        return $this->redirectToRoute('admin');
    }

    #[Route('/importUbwPrlvm', name: 'app_import_ubw_prelevement')]
    public function importUbwPrlvm(): Response
    {
        $connexionKyriba = $this->stfpService->ConnexionSftp($_ENV['HOSTKYRIBA'], $_ENV['PORTKYRIBA'], $_ENV['LOGINKYRIBA'], $_ENV['PWDKYRIBA']);
        $connexionUbw = $this->stfpService->ConnexionSftp($_ENV['HOSTUBW'], $_ENV['PORTUBW'], $_ENV['LOGINUBW'], $_ENV['PWDUBW']);
        $retour = $this->kyribaService->ImportUbwPrlvm($connexionKyriba, $connexionUbw);
        if (array($retour) && !is_null($retour)) {
            foreach($retour as $arr) {
                $this->addFlash('danger', 'Transfert échoué : le fichier  '.$arr[0]->getNom()).' existe déjà';
            }
        } 
        else {
            $this->addFlash('success', 'Transfert réussi : OK');
        }
        
        return $this->redirectToRoute('admin');
    }

    #[Route('/importUbwPrlvmAcceptance', name: 'app_import_ubw_prelevement_acceptance')]
    public function importUbwPrlvmAcceptance(): Response
    {
        $connexionKyriba = $this->stfpService->ConnexionSftp($_ENV['HOSTKYRIBA'], $_ENV['PORTKYRIBA'], $_ENV['LOGINKYRIBA'], $_ENV['PWDKYRIBA']);
        $connexionUbwAcceptance = $this->stfpService->ConnexionSftp($_ENV['HOST_UNIT4_ACCEPTANCE_EXPORT'], $_ENV['PORT_UNIT4_ACCEPTANCE_EXPORT'], $_ENV['LOGIN_UNIT4_ACCEPTANCE_EXPORT'], $_ENV['PWD_UNIT4_ACCEPTANCE_EXPORT']);
        $retour = $this->kyribaService->ImportUbwPrlvm($connexionKyriba, $connexionUbwAcceptance);
        if (array($retour) && !is_null($retour)) {
            foreach($retour as $arr) {
                $this->addFlash('danger', 'Transfert échoué : le fichier  '.$arr[0]->getNom()).' existe déjà';
            }
        } else {
            $this->addFlash('success', 'Transfert réussi : OK');
        }

        return $this->redirectToRoute('admin');
    }
   
}
