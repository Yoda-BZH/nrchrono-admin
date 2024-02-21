<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use App\Repository\TeamRepository;
use App\Service\CssFileGenerator;

use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

#[AsCommand(
    name: 'team:color',
    description: 'Generate css file for team colors',
    hidden: false,
    aliases: []
)]
class TeamColorExportCommand extends Command
{
    public function __construct(
        private TeamRepository $teamRepository,
        private CssFileGenerator $cssFileGenerator,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Fetch timers')
            //->addArgument('provider', InputArgument::REQUIRED, 'Provider to use')
            //->addOption('yell', null, InputOption::VALUE_NONE, 'Si définie, la tâche criera en majuscules')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $teams = $this->teamRepository->findAll();
        $this->cssFileGenerator
            ->setTeams($teams)
            ->generate()
            ;
        return Command::SUCCESS;
    }
}
