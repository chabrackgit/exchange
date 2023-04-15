<?php

namespace App\Command;

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
    protected function configure(): void
    {
        $this
            ->addArgument('service', InputArgument::OPTIONAL, 'Nom du service à lancer')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        
        $arg1 = $input->getArgument('service');

        if (is_null($arg1)) {
            $io = new SymfonyStyle($input, $output);
            $io->note(sprintf('You passed an argument: %s', $arg1));
            $arg1 = $io->choice('Sélectionnez le traitement à éxecuter', ['exportKyribaToPs (informations provenant de Kyriba OMNES peoplesoft_import , à renommer et déposer dans le dossier /import du serveur PeopleSoft)', 'exportKyribaToUbw (informations provenant de Kyriba ubw_rdc, à renommer et déposer dans le dossier /import du serveur Ubw)', 'reportKyribaToK (informations provenant de Kyriba, à renommer et déposer dans le dossier /report du serveur PeopleSoft)', 'importPsPayment (informations provenant de PeopleSoft, à renommer et déposer dans le dossier /kyriba/peoplesoft_paiement du serveur Kyriba OMNES)', 'importUbwPrlvm (informations provenant de Ubw, à renommer et déposer dans le dossier /kyriba/ubw du serveur Kyriba OMNES)']);
            
            
            dd($arg1);
        }

        if ($input->getOption('option1')) {
            // ...
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
