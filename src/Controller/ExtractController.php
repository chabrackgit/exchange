<?php

namespace App\Controller;

use App\Entity\Fichier;
use App\Repository\FichierRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use phpseclib3\Net\SFTP;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExtractController extends AbstractController
{

    #[Route('/extract', name: 'app_extract')]
    public function index(EntityManagerInterface $em): Response
    {
        $sshPeople = new SFTP('127.0.0.1', 2222);
        $autorizePeople = $sshPeople->login('userpeople', 'user');
        $retourPeople = $autorizePeople ? 'Oui' : 'Non';

        // $sshU4bw = new SFTP('127.0.0.1', 2223);
        // $autorizeU4bw = $sshU4bw->login('useru4bw', 'user');
        // $retourU4bw = $autorizeU4bw ? 'Oui' : 'Non';

        $sshKyriba = new SFTP('127.0.0.1', 2225);
        $autorizeKyriba = $sshKyriba->login('userkyriba', 'user');
        $retourKyriba = $autorizeKyriba ? 'Oui' : 'Non';


        // $sshOvh = new SFTP('141.94.68.92');
        // $autorizeOvh = $sshOvh->login('debian', 'ERKMGghZ2V6');
        // $retourOvh = $autorizeOvh ? 'Oui' : 'Non';


        if ($autorizePeople) {
            dump('Connexion SFTP PeopleSoft : '. $retourPeople);
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

        if ($autorizeKyriba) {
//dump('Connexion SFTP Kyriba : '. $retourKyriba);

            // PARTIE EXPORT  (voir dans serveur Kyriba le bon dossier a récupérer)
            $sshKyriba->chdir('export');


//dump($sshKyriba->exec('pwd'));
            $list = $sshKyriba->rawlist();
            // vérifier le retour de $list avant de lancer nettoyagelisteFichiersDownload()
            $newList = $this->nettoyagelisteFichiersDownload($list);

            // vérification path serveur FTP peoplesoft
            dump($sshPeople->exec('pwd'));


            $sshPeople->mkdir('dossierInput');
            $sshPeople->exec('cd dossierInput');

            $compteur = 0;
            foreach ($newList as $key => $value) {
                $fichier = new Fichier();
                $filename = $value['filename'];
                $fichier->setNom($filename);
                $res = $this->getDoctrine()->getRepository(Fichier::class)->findBy(['nom' => $filename]);
                if (1 == 0) {
                    dump('correspondance');
                } else {
//dump('aucune correspondance trouvé pour le fichier :'. $filename);
                    $filenameInfo = explode('.', $filename);
                    $customer   = $filenameInfo[0];
                    $ncVersion  = $filenameInfo[1];
                    $session    = $filenameInfo[2];

                    // attribution des valeurs à l'objet Fichier
                    $fichier->setCustomer($customer);
                    $fichier->setNcVersion($ncVersion);
                    $fichier->setSession($session);

                    switch ($session) {
                        case Fichier::SESSION_EXPORT:
                            dump('EXPORT');
                            // identifiant unique
                            $uid        = $filenameInfo[3];
                            $exportType = $filenameInfo[4];
                            $template   = $filenameInfo[5];
                            $other      = $filenameInfo[6];
                            $mimeType   = $filenameInfo[7];

                            // attribution des valeurs à l'objet Fichier
                            $fichier->setUid($uid);
                            $fichier->setExportType($exportType);
                            $fichier->setTemplate($template);
                            $fichier->setOther($other);
                            $fichier->setMimeType($mimeType);

                            // traitement de renommage 
                            $entite = explode('_', $template);
                            $date = new DateTime();
                            $dateTransfert = $date->format('dmY');
                            $kyribaRename = $entite[0]. '_' .$dateTransfert. '.' .$mimeType;
                            dump($kyribaRename);
                            $fichier->setNomKyriba($kyribaRename);

                            // attribution de la source (à voir avec Malik et Hanane)
                            $fichier->setSource('peoplesoft_import');
                        
                            
                            // persistence en base de données
                            $em->persist($fichier);
                            
                            // transfert dans le serveur dans un dossier
                           if ($autorizePeople) {
                                $data = $sshKyriba->exec('cd export; cat '.$filename);
                                $sshPeople->put($kyribaRename, $data);
                                
                                // // attribution de l'etat
                                $fichier->setEtat('Traité');
                            } else {
                                $fichier->setEtat('Vide');
                            }
                            $fichier->setCreatedAt(new DateTimeImmutable());
                            $em->flush();
                            # code...
                            break;
                        
                        case Fichier::SESSION_BANKFW:
                            // timestamp internal de kyriba
                            $uid        = $filenameInfo[3];
                            $entity     = $filenameInfo[4];
                            $format     = $filenameInfo[5];
                            $way        = $filenameInfo[6];
                            $mimeType   = $filenameInfo[7];
                            # code...
                            break;
                        
                        default:
                            # code...
                            break;
                    }
                    dump($fichier);

                }
            } 
            dump($newList);
        } else {
            dump('Impossible de se connecter au serveur SFTP Kyriba !');
        }

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
