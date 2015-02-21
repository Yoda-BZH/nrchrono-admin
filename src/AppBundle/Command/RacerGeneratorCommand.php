<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use AppBundle\Entity\Team;
use AppBundle\Entity\Racer;

use Faker;

class RacerGeneratorCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('timer:generator:racer')
            ->setDescription('Generate racers')
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

        $teamsTypes = array(
            array(2, 0),
            array(5, 3),
            array(10, 5),
            array(10, 5),
            array(10, 5),
        );

        foreach($teamsTypes as $teamType) {
            $team = new Team;
            $name = sprintf('NR %s', $faker->city);
            $team
                ->setName($name)
                ->setNbHeurePause($teamType[1])
                ->setNbPerson($teamType[0])
                ;
            $em->persist($team);

            for($i = 0; $i < $teamType[0]; $i++) {
                $racer = new Racer;
                $racer
                    ->setFirstname($firstname = $faker->firstname)
                    ->setLastName($faker->lastname)
                    ->setNickname($firstname)
                    ->setPosition($i + 1)
                    ->setIdTeam($team)
                    ;

                $em->persist($racer);
            }
        }
        $em->flush();

    }
}
