<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use AppBundle\Entity\Timing;

use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class FakerRacerTimingCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('faker:racer:timing')
            ->setDescription('Create new timing for a team')
            ->addArgument('team', InputArgument::REQUIRED, 'team to use')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $teamId = $input->getArgument('team');

        $em = $this->getContainer()->get('doctrine')->getManager();

        $repoTeam = $em->getRepository('AppBundle:Team');
        $repoTiming = $em->getRepository('AppBundle:Timing');

        $team = $repoTeam->find($teamId);
        
        $nextGuesser = $this->getContainer()->get('racer.next');
        //$output->writeln('<info>getting latest racer</info>');
        /*$latestRacer = $repoTiming->getLatestRacer($teamId);
        
        $position = $latestRacer->getPosition();
        //var_dump('latest', $position, $latestRacer->getNickname());
        
        //$output->writeln('<info>getting next racer available</info>');
        $repoRacer = $em->getRepository('AppBundle:Racer');
        $nextRacer = $repoRacer->getNextRacerAvailable($team, $position);*/
        $nextRacer = $nextGuesser
            ->setTeam($team)
            ->getNext()
            ;

        if(!$nextRacer) {
            $repoRacer = $em->getRepository('AppBundle:Racer');
            $nextRacer = $repoRacer->getFirstOfTeam($team);
        }

        //var_dump($nextRacer->getNickname(), $nextRacer->getPosition());

        //$racer = $repoTeam->getNextRacer($teamId);
        $timeToWait = rand(3 * 60, 4 * 60);
        sleep($timeToWait);
        $t = new \Datetime('00:00:00');
        $t->modify(sprintf('+%d seconds', $timeToWait));
        
        $timing = new Timing;
        $timing
            ->setCreatedAt(new \Datetime)
            ->setIdRacer($nextRacer)
            ->setIsRelay(0)
            ->setTiming($t)
            ;
        
        $em->persist($timing);
        $em->flush();
        
        return 0;
    }
}
