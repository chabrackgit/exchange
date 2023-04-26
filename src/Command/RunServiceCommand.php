<?php

namespace App\Command;

use App\Service\Kyriba\KyribaRunService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'run:service',
    description: 'permet de lancer les commandes import/report/export (sélection d\'une tâche)',
)]
class RunServiceCommand extends Command
{
    private $kyribaRun;

    public function __construct(KyribaRunService $kyribaRun)
    {
        parent::__construct();
        $this->kyribaRun = $kyribaRun;
    }
    protected function configure(): void
    {
        $this
            ->addArgument('service', InputArgument::OPTIONAL, 'Nom du service à lancer')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        
        $arg1 = $input->getArgument('service');

        if (is_null($arg1)) {
            $io = new SymfonyStyle($input, $output);
            $arg1 = $io->choice('Sélectionnez le traitement à éxecuter', ['exportKyribaToPs', 'exportKyribaToUbw', 'reportKyribaToK', 'importPsPayment', 'importUbwPrlvm']);            
            switch ($arg1) {
                case 'exportKyribaToPs':
                    $retour = $this->kyribaRun->exportKyribaToPs();
                    break;
                case 'exportKyribaToUbw':
                    $retour = $this->kyribaRun->exportKyribaToUbw();
                    break;
                case 'reportKyribaToK':
                    $retour = $this->kyribaRun->reportKyribaToK();
                    break;
                case 'importPsPayment':
                    $retour = $this->kyribaRun->importPsPayment();
                    break;
                case 'importUbwPrlvm':
                    $retour = $this->kyribaRun->importUbwPrlvm();
                    break;
                default:
                    break;
            }
            if ($retour['emptyTab']) {
                if (isset($retour['fichier']) && !empty($retour['fichier'])) {
                    foreach($retour['fichier'] as $arr) {
                        $io->warning('Transfert échoué : le fichier  '.$arr[0]->getNom().' existe déjà');
                    }
                    return Command::FAILURE;
                } else {
                    $io->success('Transfert réussi : OK'); 
                    return Command::SUCCESS;
                }
            } else {
                $io->info('Aucun nouveau fichier à traiter');
                return Command::FAILURE;
            }
            if ($retour) {
                
            } else {
            }    
        }
    }
}
