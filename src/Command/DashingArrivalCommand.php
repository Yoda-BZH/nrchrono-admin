<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use App\Repository\TeamRepository;
use App\Service\RaceManager;
use App\Service\NextRacerGuesser;
use App\Service\Dashing;

use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

#[AsCommand(
    name: 'dashing:arrival',
    description: 'update arrival on dashboard',
    hidden: false,
    aliases: []
)]
class DashingArrivalCommand extends Command
{
    public function __construct(
        private TeamRepository $teamRepository,
        private RaceManager $raceManager,
        private NextRacerGuesser $nextGuesser,
        private Dashing $dashing,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('update arrival on dashboard')
            //->addArgument('team', InputArgument::REQUIRED, 'team to use')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $verbose = $input->getOption('verbose');

        //$raceManager = $this->getContainer()->get('race');
        $racerIsStarted = $this->raceManager->isStarted();

        //$racerRepository = $em->getRepository('AppBundle:Racer');
        //$timingRepository = $em->getRepository('AppBundle:Timing');

        $teams = $this->teamRepository->findAll();
        $now = new \Datetime();

        foreach($teams as $team)
        {
            if(!$racerIsStarted)
            {
                $this->dashing->send(sprintf('/widgets/team%d', $team->getId()), array('end' => ''));
                $verbose && $output->writeln('Race has not started yet ...');
            }

            $nextRacer = $this->nextGuesser
                ->setTeam($team)
                ->getNext()
                ;

            //if(!$nextRacer) {
            //    $nextRacer = $racerRepository->getFirstOfTeam($team);
            //}

            $latestRacer = $this->nextGuesser->getLatest();

            try
            {
                //$latestTeamTiming = $timingRepository->getLatestTeamTiming($team);
                $latestTeamTiming = $this->nextGuesser->getLatestTiming();
                if(!$latestTeamTiming)
                {
                    throw new \Exception();
                }
                $clock = clone $latestTeamTiming->getClock();
            }
            catch(\Exception $e)
            {
                $race = $this->raceManager->get();
                $clock = clone $race->getStart();
            }

            $arrival = clone $clock;
            $interval = new \DateInterval($nextRacer->getTimingAvg()->format('\P\TH\Hi\Ms\S'));
            $arrival->add($interval);

            $verbose && $output->writeln(sprintf('For team %s (%d), arrival is at %s',
                $team->getName(),
                $team->getId(),
                $arrival->format('Y-m-d H:i:s')
            ));

            $this->dashing->send(sprintf('/widgets/team%d', $team->getId()), array("end" => $arrival->format('Y-m-d H:i:s')));

            //$verbose && $output->writeln(sprintf('Sent payload: %s', $json));
        }

        return Command::SUCCESS;
    }
}
