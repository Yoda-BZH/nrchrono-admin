<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\NoResultException;

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
        $repoMatsport = $em->getRepository('AppBundle:Matsport');

        $team = $repoTeam->find($teamId);

        $nextGuesser = $this->getContainer()->get('racer.next');
        //$output->writeln('<info>getting latest racer</info>');
        try {
            $latestTeamTiming = $repoMatsport->findLatestForTeam($team);
            $clock = clone $latestTeamTiming->getClock();
            $output->writeln(date('c ').'Using latest team timing clock: '.$clock->format('H:i:s'));
        } catch(NoResultException $e) {
            $output->writeln('got exception: '.$e->getMessage());
            //$output->writeln('trace: '.$e->getTraceAsString());
            $race = $this->getContainer()->get('race')->get(); //$em->getRepository('AppBundle:Race')->find(1);
            $clock = clone $race->getStart();
            $output->writeln(date('c ').'Seems le first one, using the start of race: '.$clock->format('H:i:s'));
        }

        $nextRacers = $nextGuesser
            ->setTeam($team)
            ->getNexts()
            ;

        $nextRacer = $nextRacers[1];

        //if(!$nextRacer) {
        //    $repoRacer = $em->getRepository('AppBundle:Racer');
        //    $nextRacer = $repoRacer->getFirstOfTeam($team);
        //}

        //$racer = $repoTeam->getNextRacer($teamId);
        $racerTimingAvg = $nextRacer->getTimingAvg();
        $racerTimingAvgInSec = $racerTimingAvg->format('H') * 3600
                             + $racerTimingAvg->format('i') * 60
                             + $racerTimingAvg->format('s');
        if($mediumTiming == 12) {
            //$timeToWait = rand(9 * 60, 13 * 60);
            $timeRand = rand($racerTimingAvgInSec - 30, $racerTimingAvgInSec + 30);
            $timeToWait = rand(
                max(9 * 60, $timeRand),
                min(13 * 60, $timeRand)
            );
        } elseif($mediumTiming == 3) {
            //$timeToWait = rand(2.8 * 60, 4 * 60);
            $timeRand = rand($racerTimingAvgInSec - 10, $racerTimingAvgInSec + 10);
            $timeToWait = rand(
                max(2.8 * 60, $timeRand),
                min(4 * 60, $timeRand)
            );
        } elseif($mediumTiming == 8) {
            //$timeToWait = rand(2.8 * 60, 4 * 60);
            $timeRand = rand($racerTimingAvgInSec - 10, $racerTimingAvgInSec + 10);
            $timeToWait = rand(
                max(6.4 * 60, $timeRand),
                min(9.5 * 60, $timeRand)
            );
        } else {
            throw new \Exception('Bad --timing');
        }
        $t = new \Datetime('today 00:00:00');
        $t->modify($s = sprintf('+%d seconds', $timeToWait)); // +1 to compensate drift

        $clock->modify($s);
        $output->writeln(sprintf('%s %s is arriving at %s, running for %d seconds - %s, average to %s',
            date('c'),
            $nextRacer->getNickname(),
            $clock->format('Y-m-d H:i:s'),
            $timeToWait,
            $t->format('H:i:s'),
            $nextRacer->getTimingAvg()->format('H:i:s')
        ));
        sleep($timeToWait);

        //try {
        //    $timing = $repoTiming->getPrediction($nextRacer);
        //} catch(\Exception $e) {
        //    $timing = new Timing;
        //}

        //$output->writeln(date('c ').'Creating timing');
        //$timing
        //    ->setCreatedAt(new \Datetime)
        //    ->setIdRacer($nextRacer)
        //    ->setIsRelay(0)
        //    ->setTiming($t)
        //    ->setClock($clock)
        //    ->setAutomatic()
        //    ;

        $output->writeln(date('c ').'Creating matsport emulation, ran for '.$t->format('H:i:s'));
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
        //$interval = $clock->diff(new \Datetime());
        //$output->writeln(date('c ').sprintf('Interval: %s', $interval->format('%R %H:%I:%S')));

        $output->writeln(date('c ').'saving everything');
        try {
            //$em->persist($timing);
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
