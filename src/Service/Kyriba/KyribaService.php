<?php

namespace App\Service\Kyriba;

use App\Entity\Fichier;
use App\Repository\EntiteRepository;
use App\Repository\EtablissementRepository;
use phpseclib3\Net\SFTP;
use App\Repository\FichierRepository;
use App\Repository\SessionRepository;
use App\Repository\SourceRepository;
use App\Repository\TemplateCodeRepository;
use App\Repository\TypeTransfertRepository;
use App\Service\Sftp\SftpService;
use Doctrine\ORM\EntityManagerInterface;
use Fiber;

class KyribaService {

    private $fichierRepository;
    private $sessionRepository;
    private $templateCodeRepository;
    private $entiteRepository;
    private $etablissementRepository;
    private $typeTransfertRepository;
    private $sftp;
    private $em;

    public function __construct(
        FichierRepository $fichierRepository,
        SessionRepository $sessionRepository,
        TemplateCodeRepository $templateCodeRepository,
        EntiteRepository $entiteRepository,
        EtablissementRepository $etablissementRepository,
        TypeTransfertRepository $typeTransfertRepository,
        EntityManagerInterface $em,
        ) {
        $this->fichierRepository = $fichierRepository;
        $this->sessionRepository = $sessionRepository;
        $this->templateCodeRepository = $templateCodeRepository;
        $this->entiteRepository = $entiteRepository;
        $this->etablissementRepository = $etablissementRepository;
        $this->typeTransfertRepository = $typeTransfertRepository;
        $this->em = $em;
    }

    public function ExportKyriba() {

        // connexion SFTP sur le serveur Kyriba
            $sshKyriba = new SFTP('127.0.0.1', 2225);
            $autorizeKyriba = $sshKyriba->login('userkyriba', 'user');
            //$retourKyriba = $autorizeKyriba ? 'Oui' : 'Non';

        if ($autorizeKyriba) {
            
            // PARTIE EXPORT  (voir dans serveur Kyriba le bon dossier a récupérer)
            $sshKyriba->chdir('export');
            $list = $sshKyriba->rawlist();
            // vérifier le retour de $list avant de lancer nettoyagelisteFichiersDownload()
            $newList = $this->nettoyagelisteFichiersDownload($list);

            // parcourir le dossier export de Kyriba
            foreach ($newList as $key => $value) {
                $filename = $value['filename'];
                $res = $this->fichierRepository->findBy(['nom' => $filename]);
                if (1 == 0) {
                    dump('correspondance');
                } else {
                    //dump('aucune correspondance trouvé pour le fichier :'. $filename);
                    $retourRenommage = $this->traitementFichier($filename, $sshKyriba);
            
                }
            }
        } else {
            dump('Impossible de se connecter au serveur SFTP Kyriba !');
        }
    }


    public function traitementFichier($filename, $sshKyriba) {
            $fichier = new Fichier();
            $filenameInfo = explode('.', $filename);
            $customer   = $filenameInfo[0];
            $ncVersion  = $filenameInfo[1];
            $session    = $filenameInfo[2];
            // attribution des valeurs à l'objet Fichier
            $fichier->setNom($filename);
            $fichier->setCustomer($customer);
            $fichier->setNcVersion($ncVersion);
            $sess = $this->sessionRepository->findOneBy(['code' => $session]);
            $fichier->setSession($sess);

            switch ($session) {
                case Fichier::SESSION_EXPORT:
                    //dump('EXPORT');
                    $uid        = $filenameInfo[3];
                    $exportType = $filenameInfo[4];
                    $template   = $filenameInfo[5];
                    $other      = $filenameInfo[6];
                    $mimeType   = $filenameInfo[7];

                    $templateInfo = explode('_', $template);
                    if ($other == 'AFB') { 
                        $templateCode = $this->templateCodeRepository->findOneBy(['code' => $templateInfo[0]]);
                        $entite = $this->entiteRepository->findOneBy(['codeUbw' => $templateInfo[1]]);
                        // traitement de renommage  UBW
                        $kyribaRename = $template.'.'.$other.'.'.$uid.'.'.$mimeType;
                    } else {
                        $entite = $this->entiteRepository->findOneBy(['codePeopleSoft' => $templateInfo[0]]);
                        $templateCode = $this->templateCodeRepository->findOneBy(['code' => $templateInfo[1]]);
                        $date = substr($uid, 0, 8);
                        $year = substr($date, 0, 4);
                        $mont = substr($date, 4, 5);
                        $month = substr($mont, 0, 2);
                        $day = substr($date, -2);
                       
                        // traitement de renommage PEOPLESOFT
                        $kyribaRename = $templateInfo[0]. '_' .$day.$month.$year. '.' .$mimeType;
                        
                    }
                    $typeTransfert = $this->typeTransfertRepository->findOneBy(['code' => $exportType]);
                    $session = $this->sessionRepository->findOneBy(['code' => Fichier::SESSION_EXPORT]);
                    $fichier->setEntite($entite);
                    $fichier->setTemplateCode($templateCode);    
                    $fichier->setNomKyriba($kyribaRename);
                    $fichier->setCreatedAt(new \DateTimeImmutable()); 
                    $fichier->setTypeTransfert($typeTransfert);                     

                    // attribution des valeurs à l'objet Fichier
                    $fichier->setUid($uid);
                    $fichier->setOther($other);
                    $fichier->setMimeType($mimeType);
                    $fichier->setEtat('Enregistré');
              
                    // persistence en base de données
                    $this->em->persist($fichier);
                    $this->em->flush();

                    // connexion SFTP sur le serveur ubw
                    $sshUbw = new SFTP('127.0.0.1', 2223);
                    $autorizeUbw = $sshUbw->login('useru4bw', 'user');
                    $retourUbw = $autorizeUbw ? 'Oui' : 'Non';
                

                    // connexion SFTP sur le serveur PeopleSoft
                    $sshPeople = new SFTP('127.0.0.1', 2222);
                    $autorizePeople = $sshPeople->login('userpeople', 'user');
                    $retourPeople = $autorizePeople ? 'Oui' : 'Non';

                    if ($autorizePeople && $autorizeUbw) {
                       $retourTransfert = $this->envoiFichier($filename, $sshKyriba, $sshPeople, $sshUbw, $kyribaRename);
                        
                        // // attribution de l'etat Transféré
                        $fichier->setEtat('Enregistré / Transféré');
                    } else {
                        $fichier->setEtat('Vide');
                    }

                    return 'fichier EXPORT: '. $filename . ' ENREGISTRE EN BASE DE DONNEE';
                    # code...
                    break;
                
                case Fichier::SESSION_REPORT:
                    // timestamp internal de kyriba
                    $uid        = $filenameInfo[3]; // traitement a faire ( exemple 20230321-GEN2831ma-50rke-lfht5p2b)
                    $creator    = $filenameInfo[4];
                    $reportType = $filenameInfo[5];
                    $reportProcess = $filenameInfo[6];
                    $targetMimeType = $filenameInfo[7];
                    $mimeType   = $filenameInfo[8];

                    $fichier->setUid($uid);
                    $fichier->setMimeType($mimeType);

                    $etabInfo = explode('_', $reportProcess);
                    if (strpos($reportType, Fichier::TYPE_TRANSFERT_BANK) !== false) {
                        $typeTransfert = $this->typeTransfertRepository->findOneBy(['code' => Fichier::TYPE_TRANSFERT_BANK]);
                        $fichier->setTypeTransfert($typeTransfert);
                        $templateCode = $this->templateCodeRepository->findOneBy(['session' =>$typeTransfert->getSession()]);
                        $fichier->setTemplateCode($templateCode);
                    }

                    $etablissement = $this->etablissementRepository->findOneBy(['code' => $etabInfo]);
                    $fichier->setEtablissement($etablissement);
                
                    $kyribaRename = $etablissement->getLibelle(). ' ' .substr($uid, 0, 8). '.' .strtolower($targetMimeType);
                    $fichier->setNomKyriba($kyribaRename);
                    $fichier->setCreatedAt(new \DateTimeImmutable());
                    $fichier->setEtat('Enregistré');

                    // connexion SFTP sur le serveur ubw
                    $sshUbw = new SFTP('127.0.0.1', 2223);
                    $autorizeUbw = $sshUbw->login('useru4bw', 'user');
                    $retourUbw = $autorizeUbw ? 'Oui' : 'Non';
                

                    // connexion SFTP sur le serveur PeopleSoft
                    $sshPeople = new SFTP('127.0.0.1', 2222);
                    $autorizePeople = $sshPeople->login('userpeople', 'user');
                    $retourPeople = $autorizePeople ? 'Oui' : 'Non';

                    if (strpos($filename, 'REPORT') !== false) {
                        if ($autorizePeople && $autorizeUbw) {
                            $retourTransfert = $this->envoiFichier($filename, $sshKyriba, $sshPeople, $sshUbw, $kyribaRename);
                             // // attribution de l'etat Transféré
                             $fichier->setEtat('Enregistré / Transféré');
                         } else {
                             $fichier->setEtat('Vide');
                         }
                    }
                    $this->em->persist($fichier);
                    $this->em->flush();
                    return 'fichier REPORT : '. $filename . ' ENREGISTRE EN BASE DE DONNEE';
                    break;
                
                default:
                    # code...
                    break;
        }
    }

    public function envoiFichier($filename, $sshKyriba, $sshPeople, $sshUbw, $kyribaRename) {
        $data = $sshKyriba->exec('cd export; cat '.$filename);
        if (strpos($filename, 'BANK') !== false) {
            $sshUbw->chdir('Report/ubw');
            $sshUbw->put($kyribaRename, $data);
        } else if(strpos($filename, 'AFB') !== false) {
            $sshUbw->chdir('Export/ubw');
            $sshUbw->put($kyribaRename, $data);
        } else {
            $sshPeople->chdir('Export/peoplesoft');
            $sshPeople->put($kyribaRename, $data);
        }
        
        return true;
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

