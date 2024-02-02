<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use App\Repository\TeamRepository;
use App\Service\RaceManager;
use App\Service\NextRacerGuesser;

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
    )
    {
        parent::__construct();
    }

    protected function configure()
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
            if(!$racerIsStarted) {
                $verbose && $output->writeln('Race has not started yet ...');
                $url = sprintf('http://localhost:3030/widgets/team%d', $team->getId());
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(array(
                    "auth_token" => "ttrtyuijk",
                    "end" => "",
                )));
                curl_exec($curl);
            }

            $nextRacer = $this->nextGuesser
                ->setTeam($team)
                ->getNext()
                ;

            //if(!$nextRacer) {
            //    $nextRacer = $racerRepository->getFirstOfTeam($team);
            //}

            $latestRacer = $this->nextGuesser->getLatest();

            try {
                //$latestTeamTiming = $timingRepository->getLatestTeamTiming($team);
                $latestTeamTiming = $this->nextGuesser->getLatestTiming();
                if(!$latestTeamTiming) {
                    throw new \Exception();
                }
                $clock = clone $latestTeamTiming->getClock();
            } catch(\Exception $e) {
                $race = $raceManager->get();
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

            $url = sprintf('http://localhost:3030/widgets/team%d', $team->getId());
            $curl = curl_init($url);
            $verbose && curl_setopt($curl, CURLOPT_VERBOSE, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $json = json_encode(array(
                "auth_token" => "ttrtyuijk",
                "end" => $arrival->format('Y-m-d H:i:s'),
            )));
            curl_exec($curl);
            $verbose && $output->writeln(sprintf('Sent payload: %s', $json));
        }

        return Command::SUCCESS;
    }
}
