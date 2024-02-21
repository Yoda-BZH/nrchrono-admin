<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use App\Repository\TeamRepository;
use App\Repository\TimingRepository;
use App\Service\RaceManager;
use App\Service\Dashing;

use Doctrine\ORM\NoResultException;

use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

#[AsCommand(
    name: 'dashing:bestlaps',
    description: 'Best laps per team',
    hidden: false,
    aliases: []
)]
class DashingBestlapsCommand extends Command
{
    public function __construct(
        private TeamRepository $teamRepository,
        private TimingRepository $timingRepository,
        private RaceManager $raceManager,
        private Dashing $dashing,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Best laps per team')
            //->addArgument('team', InputArgument::REQUIRED, 'team to use')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $verbose = $input->getOption('verbose');

        //$raceManager = $this->getContainer()->get('race');
        $raceIsStarted = $this->raceManager->isStarted();

        $data = array();

        foreach($this->teamRepository->findAll() as $team)
        {
            if(!$raceIsStarted)
            {
                $output->writeln('race has not started yet');
                $data[] = array(
                    'label' => sprintf('%s', str_replace('NR-', '', $team->getName())),
                    'value' => '--:--',
                );
                continue;
            }
            try
            {
                $timing = $this->timingRepository->getBestTeamLap($team);
            }
            catch(NoResultException $e)
            {
                $verbose && $output->writeln('no team lap for ' . $team->getName());
                $data[] = array(
                    'label' => sprintf('%s', str_replace('NR-', '', $team->getName())),
                    'value' => 'Aucun tour',
                );
                continue;
            }
            $data[] = array(
                'label' => sprintf('%s (%s)', $timing->getRacer()->getNickname(), str_replace('NR-', '', $timing->getRacer()->getTeam()->getName())),
                'value' => $timing->getTiming()->format('H:i:s'),
            );
        }
        //$repoRanking = $em->getRepository('AppBundle:Ranking');

        //$teams = $this->teamRepository->findAll();

        #$rankingGeneral = array();
        #$rankingCategory = array();
        #foreach($teams as $team)
        #{
        #    $ranking = $repoRanking->getLatestRankingForTeam($team);
        #    if(!$ranking) {
        #        continue;
        #    }
        #
        #    $rankingGeneral[] = array(
        #        'label' => $team->getName(),
        #        'value' => $ranking->getPosition()
        #    );
        #
        #    $rankingCategory[] = array(
        #        'label' => $team->getName(),
        #        'value' => $ranking->getPoscat()
        #    );
        #}
        #
        #if(!$rankingGeneral) {
        #    return 0;
        #}

        $this->dashing->send('/widgets/bestlaps', array('items' => $data));

        return Command::SUCCESS;
    }
}
