<?php

namespace App\Service\Sftp;

use phpseclib3\Net\SFTP;


class SftpService {

    public function ConnexionSftp($host, $port, $login, $pwd,) {
        $connexion = new SFTP($host, $port);
        return ['connexion' => $connexion, 'authorized' => $connexion->login($login, $pwd)];
    }

}


