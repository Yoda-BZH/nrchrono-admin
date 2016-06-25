<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class DashingRankingCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('dashing:ranking')
            ->setDescription('Ranking')
            //->addArgument('team', InputArgument::REQUIRED, 'team to use')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $verbose = $input->getOption('verbose');
        $em = $this->getContainer()->get('doctrine')->getManager();

        $raceManager = $this->getContainer()->get('race');
        $raceIsStarted = $raceManager->isStarted();

        $repoTeam = $em->getRepository('AppBundle:Team');
        $repoRanking = $em->getRepository('AppBundle:Ranking');

        $teams = $repoTeam->findAll();

        $rankingGeneral = array();
        $rankingCategory = array();
        foreach($teams as $team)
        {
            if (!$raceIsStarted)
            {
                $rankingGeneral[] = array(
                    'label' => $team->getName(),
                    'value' => 'En attente départ'
                );
                $rankingCategory[] = array(
                    'label' => $team->getName(),
                    'value' => 'En attente départ'
                );
                continue;
            }

            $ranking = $repoRanking->getLatestRankingForTeam($team);
            if(!$ranking) {
                $rankingGeneral[] = array(
                    'label' => $team->getName(),
                    'value' => 'N/A'
                );
                $rankingCategory[] = array(
                    'label' => $team->getName(),
                    'value' => 'N/A'
                );
                continue;
            }

            $rankingGeneral[] = array(
                'label' => $team->getName(),
                'value' => $ranking->getPosition()
            );

            $rankingCategory[] = array(
                'label' => $team->getName(),
                'value' => $ranking->getPoscat()
            );
        }

        if(!$rankingGeneral) {
            return 0;
        }

        $url = 'http://localhost:3030/widgets/rankingGen';
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(array(
            "auth_token" => "ttrtyuijk",
            "items" => $rankingGeneral,
        )));
        curl_exec($curl);

        $url = 'http://localhost:3030/widgets/rankingCat';
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(array(
            "auth_token" => "ttrtyuijk",
            "items" => $rankingCategory,
        )));
        curl_exec($curl);

        return 0;
    }
}
