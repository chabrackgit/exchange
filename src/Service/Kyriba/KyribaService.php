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
            $error['emptyTab'] = true;
            if (!empty($newList)) {
                foreach ($newList as $key => $value) {
                    $filename = $value['filename'];
                    $res = $this->fichierRepository->findBy(['nom' => $filename]);
                    if ($res) {
                        $error['fichier'][] = $res;
                    } else {
                        //dump('aucune correspondance trouvé pour le fichier :'. $filename);
                        $retourRenommage = $this->traitementFichierKyribaToPs($filename, $connexionKyriba, $connexionPs);            
                    }
                }
            } else {
                $error['emptyTab'] = false;
            }
            // partie export (de kyriba vers ubw)
            // parcourir le dossier export de Kyriba
            if (!empty($error)) {
                return $error;
            }
        }
    }

    public function report($connexionKyriba, $connexionPs) {
        if ($connexionKyriba['authorized']) {
            $connexionKyriba['connexion']->chdir('kyriba');
            $connexionKyriba['connexion']->chdir('rdc_import');
            $list = $connexionKyriba['connexion']->rawlist();
            // vérifier le retour de $list avant de lancer nettoyagelisteFichiersDownload()
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
                    $retourRenommage = $this->traitementFichierReport($value['filename'], $connexionKyriba, $connexionPs);            
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

    public function traitementFichierReport($filename, $connexionKyriba, $connexionPs) {
        $fichier        = new Fichier();
        $filenameInfo   = explode('.', $filename);
        $customer       = $filenameInfo[0];
        $ncVersion      = $filenameInfo[1];
        $session        = $filenameInfo[2];
        $uid            = $filenameInfo[3]; // traitement a faire ( exemple 20230321-GEN2831ma-50rke-lfht5p2b)
        $creator        = $filenameInfo[4];
        $reportType     = $filenameInfo[5];
        $reportProcess  = $filenameInfo[6];
        $targetMimeType = $filenameInfo[7];
        $mimeType       = $filenameInfo[8];
        $uidInfo = explode('-', $uid);
        $etabInfo = explode('_', $reportProcess);
        $sess = $this->sessionRepository->findOneBy(['code' => $session]);
        if (strpos($reportType, Fichier::TYPE_TRANSFERT_BANK) !== false) {
            $typeTransfert = $this->typeTransfertRepository->findOneBy(['code' => 'SU_ACCT']);
            $fichier->setTypeTransfert($typeTransfert);
            $templateCode = $this->templateCodeRepository->findOneBy(['session' => $sess]);
            $fichier->setTemplateCode($templateCode);
        }
        $etablissement = $this->etablissementRepository->findOneBy(['code' => $etabInfo]);

        $date = $uidInfo[0];
        $year = substr($date, 2, 2);
        $mont = substr($date, 4, 5);
        $month = substr($mont, 0, 2);
        $day = substr($date, -2);
    
        $fileRename = $etablissement->getLibelle(). ' '.$uidInfo[3].' '.$day.$month.$year. '.' .strtolower($targetMimeType);
        
        // attribution des valeurs à l'objet Fichier
        $fichier->setNom($filename);
        $fichier->setCustomer($customer);
        $fichier->setNcVersion($ncVersion);
        $sess = $this->sessionRepository->findOneBy(['code' => $session]);
        $fichier->setSession($sess);
        $fichier->setUid($uid);
        $fichier->setMimeType($mimeType);
        $fichier->setTemplateCode($templateCode);
        $fichier->setEtablissement($etablissement);
        $fichier->setNomKyriba($fileRename);
        $fichier->setCreatedAt(new \DateTimeImmutable());
        $fichier->setEtat('Enregistré');

        // persistence en base de données
        $this->em->persist($fichier);
        $this->em->flush();

        if ($connexionKyriba['authorized']) {
            $retourTransfert = $this->envoiFichierReportKyribaToK($filename, $fileRename, $connexionKyriba, $connexionPs);
        }
    }

    public function envoiFichierKyribaToUbw($filename, $kyribaRename, $connexionKyriba, $connexionUbw){
        $data = $connexionKyriba['connexion']->exec('cd kyriba/ubw_rdc; cat '.$filename);
        $connexionKyriba['connexion']->exec('cd kyriba/ubw_rdc/; cp '.$filename.' ../backup/ubw_rdc_backup/; rm '.$filename);
        $connexionUbw['connexion']->chdir('import');
        $connexionUbw['connexion']->put($kyribaRename, $data);
    }

    public function envoiFichierKyribaToPs($filename, $kyribaRename, $connexionKyriba, $connexionPs){
        $data = $connexionKyriba['connexion']->exec('cd kyriba/peoplesoft_import; cat '.$filename);
        $connexionKyriba['connexion']->exec('cd kyriba/peoplesoft_import/; cp '.$filename.' ../backup/peoplesoft_import_backup/; rm '.$filename);
        $connexionPs['connexion']->chdir('import');
        $connexionPs['connexion']->put($kyribaRename, $data);

    }

    public function envoiFichierReportKyribaToK($filename, $kyribaRename, $connexionKyriba, $connexionPs){
        $data = $connexionKyriba['connexion']->exec('cd kyriba/rdc_import; cat '.$filename);
        $connexionKyriba['connexion']->exec('cd kyriba/rdc_import/; cp '.$filename.' ../backup/rdc_import_backup/; rm '.$filename);
        $connexionPs['connexion']->chdir('report');
        $connexionPs['connexion']->put($kyribaRename, $data);

    }

    public function envoiFichierPsPaymentToKyriba($filename, $kyribaRename, $connexionKyriba, $connexionPs){
        $data = $connexionPs['connexion']->exec('cd Backup/; cat '.$filename);
        $connexionPs['connexion']->exec('cd Backup/; cp '.$filename.' ../Backup2/; rm '.$filename);
        $connexionKyriba['connexion']->chdir('kyriba');
        $connexionKyriba['connexion']->chdir('peoplesoft_paiement');
        $connexionKyriba['connexion']->put($kyribaRename, $data);
    }

    //  A VOIR ERREUR OPEN CHANNEL SSH
    public function envoiFichierUbwPrlvToKyriba($filename, $kyribaRename, $connexionKyriba, $connexionUbw){
        $data = $connexionUbw['connexion']->exec('cd Backup/; cat '.$filename);
        $connexionUbw['connexion']->exec('cd Backup/; cp '.$filename.' ../Backup2/; rm '.$filename);
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


