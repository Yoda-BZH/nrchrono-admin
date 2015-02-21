<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use AppBundle\Entity\Ranking;
use AppBundle\Entity\Team;

class RankingGeneratorCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('timer:generator:ranking')
            ->setDescription('Generate rankings')
            //->addArgument('provider', InputArgument::REQUIRED, 'Provider to use')
            //->addOption('yell', null, InputOption::VALUE_NONE, 'Si définie, la tâche criera en majuscules')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();

        $repoRanking = $em->getRepository('AppBundle:Ranking');

        $repoTeam = $em->getRepository('AppBundle:Team');
        $output->writeln('getting team 1');
        $team = $repoTeam->find(1);
        if(!$team) {
            $output->writeln('cound not find team one, creating');
            $team = new Team;
            $team
                ->setName('NR Berlingots')
                ->setNbHeurePause(5)
                ->setNbPerson(10)
                ;
            $em->persist($team);
            $em->flush();
        }

        $output->writeln('gettting latest ranking for team');
        $lastRanking = $repoRanking->getLatestRankingForTeam($team);

        if (!$lastRanking)
        {
            $output->writeln('Creating first "lastRanking ...');
            $lastRanking = new Ranking();
            $lastRanking->setTime(new \Datetime('2015-02-21 20:00:00'));
        }

        $output->writeln('calculating drift');
        $timeSpent = sprintf('+%d seconds', rand(12 * 60, 15 * 60));
        $ecart = new \Datetime('2015-02-21 00:00:00');
        $ecart->modify(sprintf('+%d seconds', rand(5 * 60, 8 * 60)));
        $ranking = new Ranking;
        $ranking
            ->setPosition(rand(20, 50))
            ->setCreatedAt(new \Datetime)
            ->setTime($lastRanking->getTime()->modify($timeSpent))
            ->setTour($lastRanking->getTour() + 1)
            ->setEcart($ecart)
            ->setDistance($lastRanking->getDistance() + 4200)
            ->setSpeed(rand(15000, 22000))
            ->setBestLap(new \Datetime)
            ->setPoscat(rand(10, 25))
            ->setIdTeam($team)
            ;
        $em->persist($ranking);
        $em->flush();
    }
}
