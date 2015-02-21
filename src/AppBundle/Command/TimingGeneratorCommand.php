<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use AppBundle\Entity\Timing;
use AppBundle\Entity\Racer;

use Faker;

class TimingGeneratorCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('timer:generator:timing')
            ->setDescription('Generate timing')
            //->addArgument('provider', InputArgument::REQUIRED, 'Provider to use')
            //->addOption('yell', null, InputOption::VALUE_NONE, 'Si définie, la tâche criera en majuscules')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();

        //$repoRanking = $em->getRepository('AppBundle:Ranking');

        $faker = Faker\Factory::create('fr_FR');

        $repoRacer = $em->getRepository('AppBundle:Racer');
        $racers = $repoRacer->findAll();

        foreach($racers as $racer) {

            //$output->writeln('calculating drift');
            $timeSpent = sprintf('+%d seconds', rand(12 * 60, 15 * 60));
            //$ecart = new \Datetime('2015-02-21 00:00:00');
            //$ecart->modify(sprintf('+%d seconds', rand(5 * 60, 8 * 60)));
            $timing = new Timing;
            $timing
                ->setTiming(rand(11 * 60, 15*60))
                ->setCreatedAt(new \Datetime())
                ->setIsRelay(0)
                ->setIdRacer($racer)
                ;
            $em->persist($timing);
        }
        $em->flush();
    }
}
