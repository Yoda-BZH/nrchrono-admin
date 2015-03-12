<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use AppBundle\Entity\Ranking;

use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class FakerRankingCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('faker:ranking')
            ->setDescription('Create new timing for a team')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();

        $repoTeam = $em->getRepository('AppBundle:Team');

        $teams = $repoTeam->findAll();

        foreach($teams as $team) {
            $position = 30 + $team->getId() * 10 + rand(0, 50);
            $ranking = new Ranking();
            $ranking
                ->setIdTeam($team)
                ->setPosition($position)
                ->setCreatedAt(new \Datetime())
                ->setTime(new \Datetime())
                ->setTour(1)
                ->setEcart(
                    new \Datetime(
                        sprintf(
                            '00:%02d:%0d',
                            rand(3,  4),
                            rand(0, 59)
                        )
                    )
                )
                ->setDistance(1)
                ->setSpeed(rand(15, 30))
                ->setBestlap(new \Datetime())
                ->setPoscat($position - 20)
                ;
            $em->persist($ranking);
        }

        $em->flush();

        return 0;
    }
}
