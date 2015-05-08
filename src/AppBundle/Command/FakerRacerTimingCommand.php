<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use AppBundle\Entity\Timing;
use AppBundle\Entity\Matsport;

use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class FakerRacerTimingCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('faker:racer:timing')
            ->setDescription('Create new timing for a team')
            ->addArgument('team', InputArgument::REQUIRED, 'team to use')
            ->addArgument('timing', InputArgument::OPTIONAL, 'timing to use', 12)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $teamId = $input->getArgument('team');
        $mediumTiming = $input->getArgument('timing', 12);

        $em = $this->getContainer()->get('doctrine')->getManager();

        $repoTeam = $em->getRepository('AppBundle:Team');
        $repoTiming = $em->getRepository('AppBundle:Timing');

        $team = $repoTeam->find($teamId);

        $nextGuesser = $this->getContainer()->get('racer.next');
        //$output->writeln('<info>getting latest racer</info>');
        try {
            $latestTeamTiming = $repoTiming->getLatestTeamTiming($team);
            $clock = clone $latestTeamTiming->getClock();
            $output->writeln(date('c ').'Using latest team timing clock');
        } catch(\Exception $e) {
            $output->writeln('got exception: '.$e->getMessage());
            //$output->writeln('trace: '.$e->getTraceAsString());
            $race = $em->getRepository('AppBundle:Race')->find(1);
            $clock = clone $race->getStart();
            $output->writeln(date('c ').'Seems le first one, using the start of race');
        }

        $nextRacer = $nextGuesser
            ->setTeam($team)
            ->getNext()
            ;

        //if(!$nextRacer) {
        //    $repoRacer = $em->getRepository('AppBundle:Racer');
        //    $nextRacer = $repoRacer->getFirstOfTeam($team);
        //}

        //$racer = $repoTeam->getNextRacer($teamId);
        if($mediumTiming == 12) {
            $timeToWait = rand(9 * 60, 13 * 60);
        } elseif($mediumTiming == 3) {
            $timeToWait = rand(2.8 * 60, 4 * 60);
        } else {
            throw new \Exception('Bad --timing');
        }
        sleep($timeToWait);
        $t = new \Datetime('00:00:00');
        $t->modify($s = sprintf('+%d seconds', $timeToWait - 1)); // +1 to compensate drift

        $clock->modify($s);

        try {
            $timing = $repoTiming->getPrediction($nextRacer);
        } catch(\Exception $e) {
            $timing = new Timing;
        }

        $output->writeln(date('c ').'Creating timing');
        $timing
            ->setCreatedAt(new \Datetime)
            ->setIdRacer($nextRacer)
            ->setIsRelay(0)
            ->setTiming($t)
            ->setClock($clock)
            ->setAutomatic()
            ;

        $output->writeln(date('c ').'Creating matsport emulation');
        $matsport = new Matsport;
        $matsport
            ->setCreatedAt(new \Datetime)
            ->setIdTeam($team->getId())
            ->setIsRelay(0)
            ->setTiming($t)
            ->setClock($clock)
            ->setAutomatic()
            ;

        $output->writeln(date('c ').'Calculating drift ...');
        $interval = $clock->diff($timing->getCreatedAt());
        $output->writeln(date('c ').sprintf('Interval: %s', $interval->format('%R %H:%I:%S')));

        $output->writeln(date('c ').'saving everything');
        try {
            $em->persist($timing);
            $em->persist($matsport);
            $em->flush();
        } catch (\Exception $e) {
            $output->writeln('unable to save: '.$e->getMessage());
            $output->writeln('trace: '.$e->getTraceAsString());
        }
        $output->writeln(date('c ').'done lap');

        return 0;
    }
}
