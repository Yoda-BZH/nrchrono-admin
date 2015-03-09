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
        try {
            $latestTeamTiming = $repoTiming->getLatestTeamTiming($team);
            $clock = clone $latestTeamTiming->getClock();
            $output->writeln('Using latest team timing clock');
        } catch(\Exception $e) {
            $race = $em->getRepository('AppBundle:Race')->find(1);
            $clock = clone $race->getStart();
            $output->writeln('Seems le first one, using the start of race');
        }

        $nextRacer = $nextGuesser
            ->setTeam($team)
            ->getNext()
            ;

        if(!$nextRacer) {
            $repoRacer = $em->getRepository('AppBundle:Racer');
            $nextRacer = $repoRacer->getFirstOfTeam($team);
        }

        //$racer = $repoTeam->getNextRacer($teamId);
        $timeToWait = rand(9 * 60, 13 * 60);
        sleep($timeToWait);
        $t = new \Datetime('00:00:00');
        $t->modify($s = sprintf('+%d seconds', $timeToWait));

        $clock->modify($s);

        $timing = new Timing;
        $timing
            ->setCreatedAt(new \Datetime)
            ->setIdRacer($nextRacer)
            ->setIsRelay(0)
            ->setTiming($t)
            ->setClock($clock)
            ;

        $interval = $clock->diff($timing->getCreatedAt());
        $output->writeln(sprintf('Interval: %s', $interval->format('%R %H:%I:%S')));

        $em->persist($timing);
        $em->flush();

        return 0;
    }
}
