<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class DashingRacerRunningCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('dashing:racer:running')
            ->setDescription('Racers running')
            //->addArgument('team', InputArgument::REQUIRED, 'team to use')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();

        $repoTeam = $em->getRepository('AppBundle:Team');
        $repoRacer = $em->getRepository('AppBundle:Racer');


        $teams = $repoTeam->findAll();
        $now = new \Datetime();
        $nextGuesser = $this->getContainer()->get('racer.next');

        $racers = array();
        $preparing = array();
        foreach($teams as $team)
        {
            $nextRacers = $nextGuesser
                ->setTeam($team)
                ->getNexts()
                ;
            $nextRacer = $nextRacers[0];

            //if(!$nextRacer) {
            //    $nextRacer = $repoRacer->getFirstOfTeam($team);
            //    $nextRacer2 = $repoRacer->getSecondOfTeam($team);
            //}

            $racers[] = array('label' => $team->getName(), 'value' => $nextRacer->getNickname());
            if(isset($nextRacers[1])) {
                $preparing[] = array('label' => $team->getName(), 'value' => $nextRacers[1]->getNickname());
            }
        }


        $url = 'http://localhost:3030/widgets/sur-la-piste';
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(array(
            "auth_token" => "ttrtyuijk",
            "items" => $racers,
        )));
        curl_exec($curl);

        $url = 'http://localhost:3030/widgets/preparation';
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(array(
            "auth_token" => "ttrtyuijk",
            "items" => $preparing,
        )));
        curl_exec($curl);

        return 0;
    }
}
