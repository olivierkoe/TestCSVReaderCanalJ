<?php

namespace App\Command;

use App\Service\ImportContactsService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:import-contacts',
)]
class ImportContactsCommand extends Command
{
    private string $dataDirectory;

    public function __construct(
        private ImportContactsService $importContactsService,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $this->importContactsService->importContacts($io);

        return Command::SUCCESS;
    }
}
