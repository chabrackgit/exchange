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
    description: 'Add a short description for your command',
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
            $io->note(sprintf('You passed an argument: %s', $arg1));
            $arg1 = $io->choice('Sélectionnez le traitement à éxecuter', ['exportKyribaToPs', 'exportKyribaToUbw', 'reportKyribaToK', 'importPsPayment', 'importUbwPrlvm']);
            
            switch ($arg1) {
                case 'exportKyribaToPs':
                    $retour = $this->kyribaRun->exportKyribaToPs();
                    if (is_array($retour)) {
                        if ($retour['empty']) {
                            $io->info('Transfert échoué: aucun fichier dans le dossier kyriba/peoplesoft_import');
                            return Command::FAILURE;
                        }
                        if (!empty($retour['fichier'])) {
                            $io->info('Transfert échoué pour '.count($retour['fichier']).' fichier existant déjà en base de données');
                            return Command::FAILURE;
                        }
                    }
                    if (!$retour['empty']) {
                        $io->info('Transfert réussi');
                        return Command::SUCCESS;
                    }
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
            if ($retour) {
                return Command::SUCCESS;
            } else {
                return Command::FAILURE;
            }    
        }
    }
}
