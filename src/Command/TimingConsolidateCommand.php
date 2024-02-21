<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use App\Service\TimingConsolidate;

use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

#[AsCommand(
    name: 'timing:consolidate',
    description: 'Consolidate predictions',
    hidden: false,
    aliases: []
)]
class TimingConsolidateCommand extends Command
{
    public function __construct(
        private TimingConsolidate $timingConsolidate,
    )
    {
        parent::__construct();
    }

    //protected function configure(): void
    //{
    //    //$this
    //    //    ->setDescription('Consolidate racer\'s timings')
    //    //;
    //}

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->timingConsolidate->run();

        return Command::SUCCESS;
    }
}
