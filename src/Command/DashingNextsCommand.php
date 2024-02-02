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
    name: 'dashing:nexts',
    description: 'update nexts on dashboard',
    hidden: false,
    aliases: []
)]
class DashingNextsCommand extends Command
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
            ->setDescription('update nexts on dashboard')
            //->addArgument('team', InputArgument::REQUIRED, 'team to use')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $verbose = $input->getOption('verbose');

        if (!$this->raceManager->isStarted())
        {
            $verbose && $output->writeln('race has not started yet');

            return 0;
        }

        //$timingRepository = $em->getRepository('AppBundle:Timing');


        $teams = $this->teamRepository->findAll();
        $now = new \Datetime();
        #$nextGuesser = $this->getContainer()->get('racer.next');


        foreach($teams as $team)
        {
            $nextRacers = $this->nextGuesser
                ->setTeam($team)
                ->getNexts()
                ;

            //if(!$nextRacer) {
            //    $nextRacer = $this->racerRepository->getFirstOfTeam($team);
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
                $race = $this->raceManager->get();
                $clock = clone $race->getStart();
            }

            $arrival = clone $clock;

            $data = array();
            for($i = 1; $i < 6; $i++) {
                if (!isset($nextRacers[$i]))
                {
                    break;
                }
                $interval = new \DateInterval($nextRacers[$i]->getTimingAvg()->format('\P\TH\Hi\Ms\S'));
                $arrival->add($interval);

                $data[] = array(
                    'label' => $nextRacers[$i]->getNickname(),
                    'value' => $arrival->format('H:i'),
                );
            }

            $url = sprintf('http://localhost:3030/widgets/racer-next-%d', $team->getId());
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(array(
                "auth_token" => "ttrtyuijk",
                "items" => $data,
            )));
            curl_exec($curl);
        }

        return Command::SUCCESS;
    }
}
