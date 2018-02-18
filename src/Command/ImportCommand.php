<?php

namespace App\Command;

use App\Import\Importer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportCommand extends Command
{
    protected static $defaultName = 'evett:import';

    /**
     * @var Importer
     */
    protected $importer;

    /**
     * ImportCommand constructor.
     * @param Importer $importer
     */
    public function __construct(Importer $importer)
    {
        parent::__construct();
        $this->importer = $importer;
    }

    protected function configure()
    {
        $this
            ->setDescription('Import events')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $this->importer->import(new \DateTime());

        $io->success('Import complete');
    }
}
