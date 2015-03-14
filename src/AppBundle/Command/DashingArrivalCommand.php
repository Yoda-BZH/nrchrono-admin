<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class DashingArrivalCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('dashing:arrival')
            ->setDescription('update arrival on dashboard')
            //->addArgument('team', InputArgument::REQUIRED, 'team to use')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();

        $repoTeam = $em->getRepository('AppBundle:Team');
        $repoRacer = $em->getRepository('AppBundle:Racer');
        //$repoTiming = $em->getRepository('AppBundle:Timing');


        $teams = $repoTeam->findAll();
        $now = new \Datetime();
        $nextGuesser = $this->getContainer()->get('racer.next');

        foreach($teams as $team)
        {
            $nextRacer = $nextGuesser
                ->setTeam($team)
                ->getNext()
                ;

            if(!$nextRacer) {
                $nextRacer = $repoRacer->getFirstOfTeam($team);
            }

            $latestRacer = $nextGuesser->getLatest();

            try {
                //$latestTeamTiming = $repoTiming->getLatestTeamTiming($team);
                $latestTeamTiming = $nextGuesser->getLatestTiming();
                if(!$latestTeamTiming) {
                    throw new \Exception();
                }
                $clock = clone $latestTeamTiming->getClock();
            } catch(\Exception $e) {
                $race = $em->getRepository('AppBundle:Race')->find(1);
                $clock = clone $race->getStart();
            }

            $arrival = clone $clock;
            $interval = new \DateInterval($nextRacer->getTimingAvg()->format('\P\TH\Hi\Ms\S'));
            $arrival->add($interval);

            $url = sprintf('http://localhost:3030/widgets/team%d', $team->getId());
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(array(
                "auth_token" => "ttrtyuijk",
                "end" => $arrival->format('Y-m-d H:i:s'),
            )));
            curl_exec($curl);
        }

        return 0;
    }
}
