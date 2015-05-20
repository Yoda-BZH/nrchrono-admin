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
        $em = $this->getContainer()->get('doctrine')->getManager();

        $repoTeam = $em->getRepository('AppBundle:Team');
        $repoRanking = $em->getRepository('AppBundle:Ranking');

        $teams = $repoTeam->findAll();

        $rankings = array();
        foreach($teams as $team)
        {
            $ranking = $repoRanking->getLatestRankingForTeam($team);
            if(!$ranking) {
                continue;
            }

            $rankings[] = array(
                'label' => $team->getName(),
                'value' => $ranking->getPosition()
            );
        }

        if(!$rankings) {
            return 0;
        }

        $url = 'http://localhost:3030/widgets/ranking';
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(array(
            "auth_token" => "ttrtyuijk",
            "items" => $rankings,
        )));
        curl_exec($curl);

        return 0;
    }
}
