<?php

namespace App\Controller;

use phpseclib3\Net\SFTP;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExtractController extends AbstractController
{
    #[Route('/extract', name: 'app_extract')]
    public function index(): Response
    {
        $sshPeople = new SFTP('127.0.0.1', 2222);
        $autorizePeople = $sshPeople->login('userpeople', 'user');
        $retourPeople = $autorizePeople ? 'Oui' : 'Non';

        // $sshU4bw = new SFTP('127.0.0.1', 2223);
        // $autorizeU4bw = $sshU4bw->login('useru4bw', 'user');
        // $retourU4bw = $autorizeU4bw ? 'Oui' : 'Non';


        // $sshOvh = new SFTP('141.94.68.92');
        // $autorizeOvh = $sshOvh->login('debian', 'ERKMGghZ2V6');
        // $retourOvh = $autorizeOvh ? 'Oui' : 'Non';


        if ($autorizePeople) {
            $sshPeople->mkdir('prevelements');
            $sshPeople->mkdir('virements');
            $sshPeople->chdir('virements');
            $locationP = $sshPeople->exec('pwd');
            dump('Connexion SFTP PeopleSoft : '. $retourPeople);
            dump($locationP);

            $filename = $sshPeople->get('filenametest');
            $list = $sshPeople->rawlist();
            $newList = $this->nettoyagelisteFichiersDownload($list);
            dump($newList);
            dump($filename);
        } else {
            dump('Impossible de se connecter au serveur SFTP PEOPLESOFT !');
        }
        // if ($autorizeU4bw) {
        //     $sshU4bw->mkdir('prevelements');
        //     $sshU4bw->mkdir('virements');
        //     $locationU = $sshU4bw->exec('ls -lrt');
        //     dump('Connexion SFTP U4bw : '.$retourU4bw);
        //     dump($locationU);
        // } else {
        //     dump('Impossible de se connecter au serveur SFTP U4BW !');
        // }

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

    public function nettoyagelisteFichiersDownload(array $tab)
    {
        $element1 ='.';
        $element2 = '..';
        unset($tab[$element1]);
        unset($tab[$element2]);
        return $tab;
    }
}
