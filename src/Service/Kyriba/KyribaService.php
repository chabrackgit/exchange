<?php

namespace App\Service\Kyriba;

use App\Entity\Fichier;
use App\Repository\EntiteRepository;
use App\Repository\EtablissementRepository;
use App\Repository\FichierRepository;
use App\Repository\SessionRepository;
use App\Repository\TemplateCodeRepository;
use App\Repository\TypeTransfertRepository;
use App\Service\Ubw\UbwService;
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
            $templateCode = $this->templateCodeRepository->findOneBy(['code' => Fichier::TEMPLATE_CODE_EXPORT_UBW]);
            $connexionKyriba['connexion']->chdir($templateCode->getDossier());
            $listFichiers = $this->nettoyagelisteFichiersDownload($connexionKyriba['connexion']->rawlist());
            $error = [];
            $error['emptyTab'] = true;
            if (!empty($listFichiers)) {
                foreach ($listFichiers as $key => $value) {
                    $filename = $value['filename'];
                    $res = $this->fichierRepository->findBy(['nom' => $filename]);
                    if ($res) {
                        $error['fichier'][] = $res;
                    } else {             
                        $this->traitementFichierKyribaToUbw($value, $connexionKyriba, $connexionUbw);            
                    }
                }
            } else {
                $error['emptyTab'] = false;
            }
            if (!empty($error)) {
                return $error;
            }
        }
    }

    public function KyribaToPs($connexionKyriba, $connexionPs) {
        if ($connexionKyriba['authorized']) {
            $templateCode = $this->templateCodeRepository->findOneBy(['code' => Fichier::TEMPLATE_CODE_EXPORT_PS]);
            $connexionKyriba['connexion']->chdir($templateCode->getDossier());
            $listFichiers = $this->nettoyagelisteFichiersDownload($connexionKyriba['connexion']->rawlist());
            $error = [];
            $error['emptyTab'] = true;
            if (!empty($listFichiers)) {
                foreach ($listFichiers as $key => $value) {
                    $filename = $value['filename'];
                    $res = $this->fichierRepository->findBy(['nom' => $filename]);
                    if ($res) {
                        $error['fichier'][] = $res;
                    } else {
                        $this->traitementFichierKyribaToPs($filename, $connexionKyriba, $connexionPs);            
                    }
                }
            } else {
                $error['emptyTab'] = false;
            }
            if (!empty($error)) {
                return $error;
            }
        }
    }

    public function report($connexionKyriba, $connexionPs) {
        if ($connexionKyriba['authorized']) {
            $templateCode = $this->templateCodeRepository->findOneBy(['code' => Fichier::TEMPLATE_CODE_REPORT]);
            $connexionKyriba['connexion']->chdir($templateCode->getDossier());
            $listFichiers = $this->nettoyagelisteFichiersDownload($connexionKyriba['connexion']->rawlist());
            $error = [];
            $error['emptyTab'] = true;
            if (!empty($listFichiers)) {
                foreach ($listFichiers as $key => $value) {
                    $filename = $value['filename'];
                    $res = $this->fichierRepository->findBy(['nom' => $filename]);
                    if ($res) {
                        $error['fichier'][] = $res;
                    } else {
                        $this->traitementFichierReport($filename, $connexionKyriba, $connexionPs);            
                    }
                }
            } else {
                $error['emptyTab'] = false;
            }
            if (!empty($error)) {
                return $error;
            }
        }
    }

    public function ImportPsPayment($connexionKyriba, $connexionPs) {
        if ($connexionPs['authorized']) {
            $templateCode = $this->templateCodeRepository->findOneBy(['code' => Fichier::TEMPLATE_CODE_EXPORT_PS]);
            $connexionPs['connexion']->chdir($templateCode->getCheminImport());
            $listFichiers = $this->nettoyagelisteFichiersDownload($connexionPs['connexion']->rawlist());
            $error = [];
            $error['emptyTab'] = true;
            if (!empty($listFichiers)) {
                foreach ($listFichiers as $key => $value) {
                    $filename = $value['filename'];
                    $res = $this->fichierRepository->findBy(['nom' => $filename]);
                    if ($res) {
                        $error['fichier'][] = $res;
                    } else {
                        $this->traitementImportPsPayment($value, $connexionKyriba, $connexionPs);            
                    }
                }
            } else {
                $error['emptyTab'] = false;
            }
            if (!empty($error)) {
                return $error;
            }   
        }
    }

    public function ImportUbwPrlvm($connexionKyriba, $connexionUbw) {
        if ($connexionUbw['authorized']) {
            $templateCode = $this->templateCodeRepository->findOneBy(['code' => Fichier::TEMPLATE_CODE_EXPORT_UBW]);
            $connexionUbw['connexion']->chdir($templateCode->getCheminImport());
            $listFichiers = $this->nettoyagelisteFichiersDownload($connexionUbw['connexion']->rawlist());
            $error = [];
            $error['emptyTab'] = true;
            if (!empty($listFichiers)) {
                foreach ($listFichiers as $key => $value) {
                    $filename = $value['filename'];
                    $res = $this->fichierRepository->findBy(['nom' => $filename]);
                    if ($res) {
                        $error['fichier'][] = $res;
                    } else {
                        $this->traitementImportUbwPrlvm($value, $connexionKyriba, $connexionUbw);            
                    }
                }
            } else {
                $error['emptyTab'] = false;
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

        $filenameInfo   = explode('.', $filename);
        $customer       = $filenameInfo[0];
        $ncVersion      = $filenameInfo[1];
        $session        = $filenameInfo[2];
        $uid            = $filenameInfo[3];
        $exportType     = $filenameInfo[4];
        $template       = $filenameInfo[5];
        $other          = $filenameInfo[6];
        $mimeType       = $filenameInfo[7];

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
        $fichier->setNom($filename)
                ->setCustomer($customer)
                ->setNcVersion($ncVersion)
                ->setUid($uid)
                ->setOther($other)
                ->setMimeType($mimeType)
                ->setEtat('Enregistré')
                ->setEntite($entite)
                ->setTemplateCode($templateCode)   
                ->setNomKyriba($kyribaRename)
                ->setCreatedAt(new \DateTimeImmutable())
                ->setTypeTransfert($typeTransfert);  
    
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
    
    public function traitementFichierKyribaToUbw($value, $connexionKyriba, $connexionUbw) {
        $fichier = new Fichier();

        $filenameInfo   = explode('.', $value['filename']);
        $customer       = $filenameInfo[0];
        $ncVersion      = $filenameInfo[1];
        $session        = $filenameInfo[2];
        $uid            = $filenameInfo[3];
        $exportType     = $filenameInfo[4];
        $template       = $filenameInfo[5];
        $other          = $filenameInfo[6];
        $mimeType       = $filenameInfo[7];

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
        $fichier->setNom($value['filename'])
                ->setCustomer($customer)
                ->setNcVersion($ncVersion)
                ->setUid($uid)
                ->setOther($other)
                ->setMimeType($mimeType)
                ->setEtat('Enregistré')
                ->setEntite($entite)
                ->setTemplateCode($templateCode)  
                ->setNomKyriba($kyribaRename)
                ->setCreatedAt(new \DateTimeImmutable())
                ->setTypeTransfert($typeTransfert);
                                // persistence en base de données
        $this->em->persist($fichier);
        $this->em->flush();


        // $donnee=file($value['filename']); 
        // dd($donnee);
        // $fichier=fopen('info.txt',"w");
        // fputs($fichier,'');
        // $i=0;
        // foreach($donnee as $d) 
        // {
        //     $keyLine = str_pad($i++, 5, 0, STR_PAD_LEFT);
        //     fputs($fichier,$keyLine.' '.$d);
        // }
        // fclose($fichier);

        // dump($value);
        // dd($kyribaRename);


        
        
        if ($connexionUbw['authorized']) {
            $retourTransfert = $this->envoiFichierKyribaToUbw($value, $kyribaRename, $connexionKyriba, $connexionUbw);
            // $retourTransfert = $this->updateFileUbw($value, $kyribaRename, $connexionKyriba, $connexionUbw);
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
        $sess = $this->sessionRepository->findOneBy(['code' => $session]);
    
        // attribution des valeurs à l'objet Fichier
        $fichier->setNom($filename)
                ->setCustomer($customer)
                ->setNcVersion($ncVersion)
                ->setSession($sess)
                ->setUid($uid)
                ->setMimeType($mimeType)
                ->setTemplateCode($templateCode)
                ->setEtablissement($etablissement)
                ->setNomKyriba($fileRename)
                ->setCreatedAt(new \DateTimeImmutable())
                ->setEtat('Enregistré');

        // persistence en base de données
        $this->em->persist($fichier);
        $this->em->flush();

        if ($connexionKyriba['authorized']) {
            $retourTransfert = $this->envoiFichierReportKyribaToK($filename, $fileRename, $connexionKyriba, $connexionPs);
        }
    }

    public function envoiFichierKyribaToUbw($value, $kyribaRename, $connexionKyriba, $connexionUbw){
        $info = $connexionKyriba['connexion']->get($value['filename']);
        //$data = $connexionKyriba['connexion']->exec('cd kyriba/ubw_rdc; cat '.$value['filename']);
        $i = 0;
        while($info) {
            $keyLine = str_pad($i++, 5, 0, STR_PAD_LEFT);
            dd($keyLine.' '.nl2br(fgets($info)));
         }


        foreach($info as $d) 
        {
            $keyLine = str_pad($i++, 5, 0, STR_PAD_LEFT);
            dd($keyLine);
            fputs($value['filename'] ,$keyLine.' '.$d);
        }
        $connexionKyriba['connexion']->exec('cd kyriba/ubw_rdc/; cp '.$value['filename'].' ../backup/ubw_rdc_backup/; rm '.$value['filename']);
        $connexionUbw['connexion']->chdir('import');
        $connexionUbw['connexion']->put($kyribaRename, $info);
    }


    public function envoiFichierKyribaToPs($filename, $kyribaRename, $connexionKyriba, $connexionPs){
        $templateCode = $this->templateCodeRepository->findOneBy(['code' => Fichier::TEMPLATE_CODE_EXPORT_PS]);
        $data = $connexionKyriba['connexion']->exec('cd '.$templateCode->getDossier().'; cat '.$filename);
        $connexionKyriba['connexion']->exec('cd '.$templateCode->getDossier().'/; cp '.$filename.' ../'.$templateCode->getCheminBackup().'/; rm '.$filename);
        $connexionPs['connexion']->chdir($templateCode->getCheminImport());
        $connexionPs['connexion']->put($kyribaRename, $data);

    }

    public function envoiFichierReportKyribaToK($filename, $kyribaRename, $connexionKyriba, $connexionPs){
        $templateCode = $this->templateCodeRepository->findOneBy(['code' => Fichier::TEMPLATE_CODE_REPORT]);
        $data = $connexionKyriba['connexion']->exec('cd '.$templateCode->getDossier().'; cat '.$filename);
        $connexionKyriba['connexion']->exec('cd '.$templateCode->getDossier().'; cp '.$filename.' ../'.$templateCode->getCheminBackup().'; rm '.$filename);
        $connexionPs['connexion']->chdir($templateCode->getCheminImport());
        $connexionPs['connexion']->put($kyribaRename, $data);

    }

    public function envoiFichierPsPaymentToKyriba($filename, $kyribaRename, $connexionKyriba, $connexionPs){
        $data = $connexionPs['connexion']->exec('cd Export/; cat '.$filename);
        $connexionPs['connexion']->exec('cd Export/; cp '.$filename.' ../backup/; rm '.$filename);
        $connexionKyriba['connexion']->chdir('kyriba');
        $connexionKyriba['connexion']->chdir('peoplesoft_paiement');
        $connexionKyriba['connexion']->put($kyribaRename, $data);
    }

    //  A VOIR ERREUR OPEN CHANNEL SSH
    public function envoiFichierUbwPrlvToKyriba($filename, $kyribaRename, $connexionKyriba, $connexionUbw){
        $data = $connexionUbw['connexion']->exec('cd Export/; cat '.$filename);
        $connexionUbw['connexion']->exec('cd Export/; cp '.$filename.' ../backup/; rm '.$filename);
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


