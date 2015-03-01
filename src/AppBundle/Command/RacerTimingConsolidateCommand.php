<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class RacerTimingConsolidateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('racer:timing:consolidate')
            ->setDescription('Consolidate racer\'s timings ')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();

        $repoRacer = $em->getRepository('AppBundle:Racer');
        $repoTiming = $em->getRepository('AppBundle:Timing');

        $racers = $repoRacer->getAll();
        foreach($racers as $racer)
        {
            //$output->writeln('Consolidating '.$racer->getNickname());
            $stats = $repoTiming->getStats($racer);
            if (!$stats)
            {
                $output->writeln(sprintf('It seems %s has no timing, skipping', $racer->getNickname()));
                continue;
            }
            //var_dump($stats);
            $tMin = new \Datetime($stats[1]);
            $tMax = new \Datetime($stats[2]);
            $tAvg = new \Datetime('00:00:00');
            $tAvg->modify(sprintf('+%d seconds', $stats[3]));
            $racer->setTimingMin($tMin);
            $racer->setTimingMax($tMax);
            $racer->setTimingAvg($tAvg);
            $em->persist($racer);
            $em->flush();
        }
    }
}
