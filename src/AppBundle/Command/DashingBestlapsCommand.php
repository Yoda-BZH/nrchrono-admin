<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class DashingBestlapsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('dashing:bestlaps')
            ->setDescription('Best laps per team')
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

        //$repoTeam = $em->getRepository('AppBundle:Team');
        $repoTiming = $em->getRepository('AppBundle:Timing');
        $timings = $repoTiming->getBestTeamLaps();
        $data = array();
        foreach($timings as $timing) {
                $data[] = array(
                'label' => sprintf('%s (%s)', $timing['nickname'], str_replace('NR-', '', $timing['name'])),
                'value' => $timing[1],
            );
        }
        //$repoRanking = $em->getRepository('AppBundle:Ranking');

        //$teams = $repoTeam->findAll();

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

        $url = 'http://localhost:3030/widgets/bestlaps';
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(array(
            "auth_token" => "ttrtyuijk",
            "items" => $data,
        )));
        curl_exec($curl);

        return 0;
    }
}
