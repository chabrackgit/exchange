<?php

namespace App\Service\Kyriba;

use App\Entity\Fichier;
use App\Repository\EntiteRepository;
use App\Repository\EtablissementRepository;
use App\Repository\FichierRepository;
use App\Repository\SessionRepository;
use App\Repository\TemplateCodeRepository;
use App\Repository\TypeTransfertRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

class KyribaService {

    private $fichierRepository;
    private $sessionRepository;
    private $templateCodeRepository;
    private $entiteRepository;
    private $etablissementRepository;
    private $typeTransfertRepository;
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

    public function ExportKyriba($connexionKyriba, $connexionUbw, $connexionPs) {

        if ($connexionKyriba['authorized']) {
            
            // PARTIE EXPORT  (voir dans serveur Kyriba le bon dossier a récupérer)
            $connexionKyriba['connexion']->chdir('kyriba');
            $connexionKyriba['connexion']->chdir('export');
            $list = $connexionKyriba['connexion']->rawlist();
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
                    $retourRenommage = $this->traitementFichier($filename, $connexionKyriba, $connexionUbw, $connexionPs);
            
                }
            }
        } else {
            dump('Impossible de se connecter au serveur SFTP Kyriba !');
        }
    }

    public function KyribaToUbw($connexionKyriba, $connexionUbw) {
        if ($connexionKyriba['authorized']) {
            $connexionKyriba['connexion']->chdir('kyriba');
            $connexionKyriba['connexion']->chdir('ubw_rdc');
            $list = $connexionKyriba['connexion']->rawlist();
            // vérifier le retour de $list avant de lancer nettoyagelisteFichiersDownload()
            $newList = $this->nettoyagelisteFichiersDownload($list);
            $error = [];
            // partie export (de kyriba vers ubw)
            // parcourir le dossier export de Kyriba
            foreach ($newList as $key => $value) {
                $filename = $value['filename'];
                $res = $this->fichierRepository->findBy(['nom' => $filename]);
                if ($res) {
                    $error[] = $res;
                } else {
                    //dump('aucune correspondance trouvé pour le fichier :'. $filename);
                    $retourRenommage = $this->traitementFichierKyribaToUbw($filename, $connexionKyriba, $connexionUbw);            
                }
            }
            if (!empty($error)) {
                return $error;
            }
        }
    }

    public function KyribaToPs($connexionKyriba, $connexionPs) {
        if ($connexionKyriba['authorized']) {
            $connexionKyriba['connexion']->chdir('kyriba');
            $connexionKyriba['connexion']->chdir('peoplesoft_import');
            $list = $connexionKyriba['connexion']->rawlist();
            // vérifier le retour de $list avant de lancer nettoyagelisteFichiersDownload()
            $newList = $this->nettoyagelisteFichiersDownload($list);
            $error = [];
            // partie export (de kyriba vers ubw)
            // parcourir le dossier export de Kyriba
            foreach ($newList as $key => $value) {
                $filename = $value['filename'];
                $res = $this->fichierRepository->findBy(['nom' => $filename]);
                if ($res) {
                    $error[] = $res;
                } else {
                    //dump('aucune correspondance trouvé pour le fichier :'. $filename);
                    $retourRenommage = $this->traitementFichierKyribaToPs($filename, $connexionKyriba, $connexionPs);            
                }
            }
            if (!empty($error)) {
                return $error;
            }
        }
    }

    public function ImportPsPayment($connexionKyriba, $connexionPs) {
        if ($connexionPs['authorized']) {
            $connexionPs['connexion']->chdir('Backup');
            $list = $connexionPs['connexion']->rawlist();
            // vérifier le retour de $list avant de lancer nettoyagelisteFichiersDownload()
            $newList = $this->nettoyagelisteFichiersDownload($list);
            $error = [];
            // partie export (de kyriba vers ubw)
            // parcourir le dossier export de Kyriba
            foreach ($newList as $key => $value) {
                $filename = $value['filename'];
                $res = $this->fichierRepository->findBy(['nom' => $filename]);
                if ($res) {
                    $error[] = $res;
                } else {
                    //dump('aucune correspondance trouvé pour le fichier :'. $filename);
                    $traitement = $this->traitementImportPsPayment($value, $connexionKyriba, $connexionPs);
                                
                }
            }
            if (!empty($error)) {
                return $error;
            }
        
        }
    }

    public function ImportUbwPrlvm($connexionKyriba, $connexionUbw) {
        if ($connexionUbw['authorized']) {
            $connexionUbw['connexion']->chdir('Backup');
            $list = $connexionUbw['connexion']->rawlist();            // vérifier le retour de $list avant de lancer nettoyagelisteFichiersDownload()
            $newList = $this->nettoyagelisteFichiersDownload($list);
            $error = [];
            // partie export (de kyriba vers ubw)
            // parcourir le dossier export de Kyriba
            foreach ($newList as $key => $value) {
                $res = $this->fichierRepository->findBy(['nom' => $value['filename']]);
                if ($res) {
                    $error[] = $res;
                } else {
                    //dump('aucune correspondance trouvé pour le fichier :'. $filename);
                    $this->traitementImportUbwPrlvm($value, $connexionKyriba, $connexionUbw);         
                }
            }
            if (!empty($error)) {
                return $error;
            }
        }
    }

    public function traitementImportUbwPrlvm($value, $connexionKyriba, $connexionUbw) {
        $filenameInfo = explode('_', $value['filename']);
        
        $fichier = new Fichier();
        $mimeType = 'txt';
        $filenameInfo = explode('_', $value['filename']);

        // récupération des objets
        $entite = $this->entiteRepository->findOneBy(['codeUbw' => $filenameInfo[0]]);
        $session = $this->sessionRepository->findOneBy(['code' => Fichier::SESSION_IMPORT]);
        $dateCreated = new DateTimeImmutable();
        $uid = 'UBW_'.$dateCreated->getTimestamp().substr($value['atime'], strlen($value['atime']) - 6);
        $typeTransfert = $this->typeTransfertRepository->findOneBy(['code'=> 'PY_BULK']);
        $templateCode = $this->templateCodeRepository->findOneBy(['code' => 'SDD']);

        // renommage pour peoplesoft_paiement
        $fileRename = Fichier::KYRIBA_CUSTOMER.'.'.Fichier::KYRIBA_NCVERSION.'.'.$session->getcode().'.'.$uid.'.'.$typeTransfert->getCode().'.'.$entite->getCodeKyriba().'_'.$templateCode->getCode().'.'.'null'.'.'.'null'.'.'.$mimeType;
        
        // attribution des valeurs à l'objet Fichier
        $fichier->setNom($value['filename'])
            ->setCustomer(Fichier::KYRIBA_CUSTOMER)
            ->setNcVersion(Fichier::KYRIBA_NCVERSION)
            ->setUid($uid)
            ->setOther(null)
            ->setMimeType($mimeType)
            ->setEtat('Enregistré')
            ->setEntite($entite)
            ->setTemplateCode($templateCode)   
            ->setNomKyriba($fileRename)
            ->setSession($session)
            ->setCreatedAt(new \DateTimeImmutable()) 
            ->setTypeTransfert($typeTransfert);

        //persistence des données
        $this->em->persist($fichier);
        $this->em->flush();

        if ($connexionKyriba['authorized']) {
            $retourTransfert = $this->envoiFichierUbwPrlvToKyriba($value['filename'], $fileRename, $connexionKyriba, $connexionUbw);
         }

    }

    public function traitementImportPsPayment($value, $connexionKyriba, $connexionPs) {
        $fichier = new Fichier();
        $mimeType = 'txt';
        $filenameInfo = explode('_', $value['filename']);

        // récupération des objets
        $entite = $this->entiteRepository->findOneBy(['codePeopleSoft'=> $filenameInfo[0]]);
        $session = $this->sessionRepository->findOneBy(['code' => Fichier::SESSION_IMPORT]);
        $dateCreated = new DateTimeImmutable();
        $uid = 'PPS_'.$dateCreated->getTimestamp().substr($value['atime'], strlen($value['atime']) - 6);
        $typeTransfert = $this->typeTransfertRepository->findOneBy(['code'=> 'PY_TRANSFER']);
        $templateCode = $this->templateCodeRepository->findOneBy(['code' => 'PS_SCT']);

        // renommage pour peoplesoft_paiement
        $fileRename = Fichier::KYRIBA_CUSTOMER.'.'.Fichier::KYRIBA_NCVERSION.'.'.$session->getcode().'.'.$uid.'.'.$typeTransfert->getCode().'.'.$templateCode->getCode().'.'.$entite->getCodeKyriba().'.null.'.$mimeType;

        // attribution des valeurs à l'objet Fichier
        $fichier->setNom($value['filename'])
            ->setCustomer(Fichier::KYRIBA_CUSTOMER)
            ->setNcVersion(Fichier::KYRIBA_NCVERSION)
            ->setUid($uid)
            ->setOther(null)
            ->setMimeType($mimeType)
            ->setEtat('Enregistré')
            ->setEntite($entite)
            ->setTemplateCode($templateCode)   
            ->setNomKyriba($fileRename)
            ->setSession($session)
            ->setCreatedAt(new \DateTimeImmutable()) 
            ->setTypeTransfert($typeTransfert);

        //persistence des données
        $this->em->persist($fichier);
        $this->em->flush();

        if ($connexionKyriba['authorized']) {
            $retourTransfert = $this->envoiFichierPsPaymentToKyriba($value['filename'], $fileRename, $connexionKyriba, $connexionPs);
         }


    }

    public function traitementFichierKyribaToPs($filename, $connexionKyriba, $connexionPs) {
        $fichier = new Fichier();
        $filenameInfo = explode('.', $filename);
        $customer   = $filenameInfo[0];
        $ncVersion  = $filenameInfo[1];
        $session    = $filenameInfo[2];
        $uid        = $filenameInfo[3];
        $exportType = $filenameInfo[4];
        $template   = $filenameInfo[5];
        $other      = $filenameInfo[6];
        $mimeType   = $filenameInfo[7];

        // Récupération de la session
        $sess = $this->sessionRepository->findOneBy(['code' => $session]);
        $fichier->setSession($sess);

        // récupération du template Code
        $templateInfo = explode('_', $template);

        // récupération du template code 
        $templateCode = $this->templateCodeRepository->findOneBy(['code' => $templateInfo[1]]);

        // récupération de l'entité
        $entite = $this->entiteRepository->findOneBy(['codePeopleSoft' => $templateInfo[0]]);

        $date = substr($uid, 0, 8);
        $year = substr($date, 0, 4);
        $mont = substr($date, 4, 5);
        $month = substr($mont, 0, 2);
        $day = substr($date, -2);

        // traitement de renommage PEOPLESOFT
        $kyribaRename = $templateInfo[0]. '_' .$day.$month.$year. '.' .$mimeType;

        // Récupération du type transfert
        $typeTransfert = $this->typeTransfertRepository->findOneBy(['code' => $exportType]);

        // attribution des valeurs à l'objet Fichier
        $fichier->setNom($filename);
        $fichier->setCustomer($customer);
        $fichier->setNcVersion($ncVersion);
        $fichier->setUid($uid);
        $fichier->setOther($other);
        $fichier->setMimeType($mimeType);
        $fichier->setEtat('Enregistré');
        $fichier->setEntite($entite);
        $fichier->setTemplateCode($templateCode);    
        $fichier->setNomKyriba($kyribaRename);
        $fichier->setCreatedAt(new \DateTimeImmutable()); 
        $fichier->setTypeTransfert($typeTransfert);  
    
        // persistence en base de données
        $this->em->persist($fichier);
        $this->em->flush();
        
        
        if ($connexionPs['authorized']) {
            $retourTransfert = $this->envoiFichierKyribaToPs($filename, $kyribaRename, $connexionKyriba, $connexionPs);
            if ($retourTransfert) { 
                $fichier->setEtat('Enregistré / Transféré');
            }
         } else {
             $fichier->setEtat('Vide');
         }
    }
    
    public function traitementFichierKyribaToUbw($filename, $connexionKyriba, $connexionUbw) {
        $fichier = new Fichier();
        $filenameInfo = explode('.', $filename);
        $customer   = $filenameInfo[0];
        $ncVersion  = $filenameInfo[1];
        $session    = $filenameInfo[2];
        $uid        = $filenameInfo[3];
        $exportType = $filenameInfo[4];
        $template   = $filenameInfo[5];
        $other      = $filenameInfo[6];
        $mimeType   = $filenameInfo[7];

        // Récupération de la session
        $sess = $this->sessionRepository->findOneBy(['code' => $session]);
        $fichier->setSession($sess);

        $templateInfo = explode('_', $template);
        
        // récupération du template Code
        $templateCode = $this->templateCodeRepository->findOneBy(['code' => $templateInfo[0]]);

        // récupération de l'entité
        $entite = $this->entiteRepository->findOneBy(['codeUbw' => $templateInfo[1]]);

        // traitement de renommage UBW
        $kyribaRename = $template.'.'.$other.'.'.$uid.'.'.$mimeType;

        $typeTransfert = $this->typeTransfertRepository->findOneBy(['code' => $exportType]);
                           

        // attribution des valeurs à l'objet Fichier
        $fichier->setNom($filename);
        $fichier->setCustomer($customer);
        $fichier->setNcVersion($ncVersion);
        $fichier->setUid($uid);
        $fichier->setOther($other);
        $fichier->setMimeType($mimeType);
        $fichier->setEtat('Enregistré');
        $fichier->setEntite($entite);
        $fichier->setTemplateCode($templateCode);    
        $fichier->setNomKyriba($kyribaRename);
        $fichier->setCreatedAt(new \DateTimeImmutable()); 
        $fichier->setTypeTransfert($typeTransfert);  
    
        // persistence en base de données
        $this->em->persist($fichier);
        $this->em->flush();
        
        
        if ($connexionUbw['authorized']) {
            $retourTransfert = $this->envoiFichierKyribaToUbw($filename, $kyribaRename, $connexionKyriba, $connexionUbw);
            if ($retourTransfert) { 
                $fichier->setEtat('Enregistré / Transféré');
            }
         } else {
             $fichier->setEtat('Vide');
         }
    }

    public function traitementFichier($filename, $connexionKyriba, $connexionUbw, $connexionPs) {
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


                    if ($connexionPs['authorized'] && $connexionUbw['authorized']) {
                       $retourTransfert = $this->envoiFichier($filename, $kyribaRename, $connexionKyriba, $connexionUbw, $connexionPs);
                        
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

                    if (strpos($filename, 'REPORT') !== false) {
                        if ($connexionPs['authorized'] && $connexionUbw['authorized']) {
                            $retourTransfert = $this->envoiFichier($filename, $kyribaRename, $connexionKyriba, $connexionUbw, $connexionPs);
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
                    # code..
                    
                    break;
        }
    }

    public function envoiFichier($filename, $kyribaRename, $connexionKyriba, $connexionUbw, $connexionPs) {
        $data = $connexionKyriba['connexion']->exec('cd export; cat '.$filename);
        if (strpos($filename, 'BANK') !== false) {
            $connexionUbw['connexion']->chdir('Report/');
            $connexionUbw['connexion']->put($kyribaRename, $data);
            $connexionUbw['connexion']->exec('cd ~');
        } else if(strpos($filename, 'AFB') !== false) {
            $connexionUbw['connexion']->chdir('ubw');
            $connexionUbw['connexion']->chdir('import');
            $connexionUbw['connexion']->put($kyribaRename, $data);
        } else {
            $connexionPs['connexion']->chdir('peoplesoft');
            $connexionPs['connexion']->chdir('import');
            $connexionPs['connexion']->put($kyribaRename, $data);
        }
        
        return true;
    }

    public function envoiFichierKyribaToUbw($filename, $kyribaRename, $connexionKyriba, $connexionUbw){
        $data = $connexionKyriba['connexion']->exec('cd kyriba/ubw_rdc; cat '.$filename);
        $connexionUbw['connexion']->chdir('import');
        $connexionUbw['connexion']->put($kyribaRename, $data);
    }

    public function envoiFichierKyribaToPs($filename, $kyribaRename, $connexionKyriba, $connexionPs){
        $data = $connexionKyriba['connexion']->exec('cd kyriba/peoplesoft_import; cat '.$filename);
        $connexionPs['connexion']->chdir('import');
        $connexionPs['connexion']->put($kyribaRename, $data);

    }

    public function envoiFichierPsPaymentToKyriba($filename, $kyribaRename, $connexionKyriba, $connexionPs){
        $data = $connexionPs['connexion']->exec('cd Backup/; cat '.$filename);
        $connexionKyriba['connexion']->chdir('kyriba');
        $connexionKyriba['connexion']->chdir('peoplesoft_paiement');
        $connexionKyriba['connexion']->put($kyribaRename, $data);
    }

    //  A VOIR ERREUR OPEN CHANNEL SSH
    public function envoiFichierUbwPrlvToKyriba($filename, $kyribaRename, $connexionKyriba, $connexionUbw){
        $data = $connexionUbw['connexion']->exec('cd Backup/; cat '.$filename);
        $connexionKyriba['connexion']->chdir('kyriba');
        $connexionKyriba['connexion']->chdir('ubw');
        $connexionKyriba['connexion']->put($kyribaRename, $data);
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


