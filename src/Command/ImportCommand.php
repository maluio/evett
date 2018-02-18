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

    private CONST numberOfDays = 14;

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

        $day = new \DateTime();

        $this->importer->import($day);
        for($i=0; $i< self::numberOfDays; $i++){
            $day = $day->add(\DateInterval::createFromDateString('1 day'));
            $this->importer->import($day);
        }

        $io->success('Import complete');
    }
}
