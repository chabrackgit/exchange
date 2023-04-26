<?php

namespace App\Service\Ubw;


class UbwService {

    public function addContentFile($file) {
        $donnee=file($file); 
        $fichier=fopen('info.txt',"w");
        fputs($fichier,'');
        $i=0;
        foreach($donnee as $d) 
        {
            $keyLine = str_pad($i++, 5, 0, STR_PAD_LEFT);
            fputs($fichier,$keyLine.' '.$d);
        }
        fclose($fichier);
        
    }
}


