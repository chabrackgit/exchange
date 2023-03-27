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

    public function __construct(KyribaService $kyribaService) {
        $this->kyribaService = $kyribaService;
    }

    #[Route('/extract', name: 'app_extract')]
    public function index(EntityManagerInterface $em): Response
    {
        $info = true;
        $retour = $this->kyribaService->ExportKyriba();

        // if ($info) {
        //     return $this->redirectToRoute('admin');
        // }
        // $sshPeople = new SFTP('127.0.0.1', 2222);
        // $autorizePeople = $sshPeople->login('userpeople', 'user');
        // $retourPeople = $autorizePeople ? 'Oui' : 'Non';

        // $sshU4bw = new SFTP('127.0.0.1', 2223);
        // $autorizeU4bw = $sshU4bw->login('useru4bw', 'user');
        // $retourU4bw = $autorizeU4bw ? 'Oui' : 'Non';

        // $sshKyriba = new SFTP('127.0.0.1', 2225);
        // $autorizeKyriba = $sshKyriba->login('userkyriba', 'user');
        // $retourKyriba = $autorizeKyriba ? 'Oui' : 'Non';

        // $sshOvh = new SFTP('141.94.68.92');
        // $autorizeOvh = $sshOvh->login('debian', 'ERKMGghZ2V6');
        // $retourOvh = $autorizeOvh ? 'Oui' : 'Non';
        

        // if ($autorizeOvh) {
        //     $pwd = $sshOvh->exec('pwd');
        //     $listFichier = $sshOvh->exec('ls -lrt');
        //     dump('Connexion SFTP OVH : '.$retourOvh);
        //     dump($pwd);
        //     dump($listFichier);
        // } else {
        //     dump('Impossible de se connecter au serveur SFTP CHABDEV OVH !');
        // }

        dd('fini');

        return $this->render('extract/index.html.twig', [
            'controller_name' => 'ExtractController',
        ]);
    }

    #[Route('/traitement', name: 'traitement')]
    public function executeTraitement(EntityManagerInterface $em): Response
    {   
        $filename = 'INSEEC.NC4.EXPORT.2023032213263448397.COMP.00941_PS.CA.txt';

        $sshKyriba = new SFTP('127.0.0.1', 2225);
            $autorizeKyriba = $sshKyriba->login('userkyriba', 'user');
            //$retourKyriba = $autorizeKyriba ? 'Oui' : 'Non';

        if ($autorizeKyriba) {
            $retour = $this->kyribaService->traitementFichier($filename, $sshKyriba);
            $this->redirectToRoute('admin');
        }
        // $sshPeople = new SFTP('127.0.0.1', 2222);
        // $autorizePeople = $sshPeople->login('userpeople', 'user');
        // $retourPeople = $autorizePeople ? 'Oui' : 'Non';

        // $sshU4bw = new SFTP('127.0.0.1', 2223);
        // $autorizeU4bw = $sshU4bw->login('useru4bw', 'user');
        // $retourU4bw = $autorizeU4bw ? 'Oui' : 'Non';

        // $sshKyriba = new SFTP('127.0.0.1', 2225);
        // $autorizeKyriba = $sshKyriba->login('userkyriba', 'user');
        // $retourKyriba = $autorizeKyriba ? 'Oui' : 'Non';

        // $sshOvh = new SFTP('141.94.68.92');
        // $autorizeOvh = $sshOvh->login('debian', 'ERKMGghZ2V6');
        // $retourOvh = $autorizeOvh ? 'Oui' : 'Non';
        

        // if ($autorizeOvh) {
        //     $pwd = $sshOvh->exec('pwd');
        //     $listFichier = $sshOvh->exec('ls -lrt');
        //     dump('Connexion SFTP OVH : '.$retourOvh);
        //     dump($pwd);
        //     dump($listFichier);
        // } else {
        //     dump('Impossible de se connecter au serveur SFTP CHABDEV OVH !');
        // }
        dd('fini');

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'ExtractController',
        ]);
    }

   
}
