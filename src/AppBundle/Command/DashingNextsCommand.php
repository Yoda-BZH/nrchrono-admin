<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class DashingNextsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('dashing:nexts')
            ->setDescription('update nexts on dashboard')
            //->addArgument('team', InputArgument::REQUIRED, 'team to use')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $verbose = $input->getOption('verbose');
        $em = $this->getContainer()->get('doctrine')->getManager();

        $raceManager = $this->getContainer()->get('race');
        if (!$raceManager->isStarted())
        {
            $verbose && $output->writeln('race has not started yet');

            return 0;
        }

        $repoTeam = $em->getRepository('AppBundle:Team');
        $repoRacer = $em->getRepository('AppBundle:Racer');
        //$repoTiming = $em->getRepository('AppBundle:Timing');


        $teams = $repoTeam->findAll();
        $now = new \Datetime();
        $nextGuesser = $this->getContainer()->get('racer.next');


        foreach($teams as $team)
        {
            $nextRacers = $nextGuesser
                ->setTeam($team)
                ->getNexts()
                ;

            //if(!$nextRacer) {
            //    $nextRacer = $repoRacer->getFirstOfTeam($team);
            //}

            $latestRacer = $nextGuesser->getLatest();

            try {
                //$latestTeamTiming = $repoTiming->getLatestTeamTiming($team);
                $latestTeamTiming = $nextGuesser->getLatestTiming();
                if(!$latestTeamTiming) {
                    throw new \Exception();
                }
                $clock = clone $latestTeamTiming->getClock();
            } catch(\Exception $e) {
                $race = $raceManager->get();
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

        return 0;
    }
}
