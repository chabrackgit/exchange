<?php

namespace App\Service\Kyriba;


use App\Service\Sftp\SftpService;
use App\Service\Kyriba\KyribaService;


class KyribaRunService {

    private $kyribaService;
    private $stfpService;

    public function __construct(
        KyribaService $kyribaService, 
        SftpService $sftpService) {
        $this->kyribaService = $kyribaService;
        $this->stfpService = $sftpService;
    }

    public function exportKyribaToPs()
    {
        $connexionKyriba = $this->stfpService->ConnexionSftp($_ENV['HOSTKYRIBA'], $_ENV['PORTKYRIBA'], $_ENV['LOGINKYRIBA'], $_ENV['PWDKYRIBA']);
        $connexionPs = $this->stfpService->ConnexionSftp($_ENV['HOSTPS'], $_ENV['PORTPS'], $_ENV['LOGINPS'], $_ENV['PWDPS']);
        $retour = $this->kyribaService->KyribaToPs($connexionKyriba, $connexionPs);
        if (array($retour) && !is_null($retour)) {
            return $retour;
        } else {
            return true;
        }
    }

    public function exportKyribaToUbw()
    {
        $connexionKyriba = $this->stfpService->ConnexionSftp($_ENV['HOSTKYRIBA'], $_ENV['PORTKYRIBA'], $_ENV['LOGINKYRIBA'], $_ENV['PWDKYRIBA']);
        $connexionUbw = $this->stfpService->ConnexionSftp($_ENV['HOSTUBW'], $_ENV['PORTUBW'], $_ENV['LOGINUBW'], $_ENV['PWDUBW']);
        $retour = $this->kyribaService->KyribaToUbw($connexionKyriba, $connexionUbw);
        if (array($retour) && !is_null($retour)) {
            return count($retour);
        } else {
            return true;
        }
    }

    public function importPsPayment()
    {
        $connexionKyriba = $this->stfpService->ConnexionSftp($_ENV['HOSTKYRIBA'], $_ENV['PORTKYRIBA'], $_ENV['LOGINKYRIBA'], $_ENV['PWDKYRIBA']);
        $connexionPs = $this->stfpService->ConnexionSftp($_ENV['HOSTPS'], $_ENV['PORTPS'], $_ENV['LOGINPS'], $_ENV['PWDPS']);
        $retour = $this->kyribaService->ImportPsPayment($connexionKyriba, $connexionPs);
        if (array($retour) && !is_null($retour)) {
            foreach($retour as $arr) {

            }
        } else {

        }
    }

    public function reportKyribaToK()
    {
        $connexionKyriba = $this->stfpService->ConnexionSftp($_ENV['HOSTKYRIBA'], $_ENV['PORTKYRIBA'], $_ENV['LOGINKYRIBA'], $_ENV['PWDKYRIBA']);
        $connexionPs = $this->stfpService->ConnexionSftp($_ENV['HOSTPS'], $_ENV['PORTPS'], $_ENV['LOGINPS'], $_ENV['PWDPS']);
        $retour = $this->kyribaService->report($connexionKyriba, $connexionPs);
        if (array($retour) && !is_null($retour)) {
            return count($retour);
        } else {
            return true;
        }
    }

    public function importUbwPrlvm()
    {
        $connexionKyriba = $this->stfpService->ConnexionSftp($_ENV['HOSTKYRIBA'], $_ENV['PORTKYRIBA'], $_ENV['LOGINKYRIBA'], $_ENV['PWDKYRIBA']);
        $connexionUbw = $this->stfpService->ConnexionSftp($_ENV['HOSTUBW'], $_ENV['PORTUBW'], $_ENV['LOGINUBW'], $_ENV['PWDUBW']);
        $retour = $this->kyribaService->ImportUbwPrlvm($connexionKyriba, $connexionUbw);
        if (array($retour) && !is_null($retour)) {
            return count($retour);
        } else {
            return true;
        }
    }

    public function importUbwPrlvmAcceptance()
    {
        $connexionKyriba = $this->stfpService->ConnexionSftp($_ENV['HOSTKYRIBA'], $_ENV['PORTKYRIBA'], $_ENV['LOGINKYRIBA'], $_ENV['PWDKYRIBA']);
        $connexionUbwAcceptance = $this->stfpService->ConnexionSftp($_ENV['HOST_UNIT4_ACCEPTANCE_EXPORT'], $_ENV['PORT_UNIT4_ACCEPTANCE_EXPORT'], $_ENV['LOGIN_UNIT4_ACCEPTANCE_EXPORT'], $_ENV['PWD_UNIT4_ACCEPTANCE_EXPORT']);
        $retour = $this->kyribaService->ImportUbwPrlvm($connexionKyriba, $connexionUbwAcceptance);
        if (array($retour) && !is_null($retour)) {
            return count($retour);
        } else {
            return true;
        }
    }

}


