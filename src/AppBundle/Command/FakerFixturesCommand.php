<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use AppBundle\Entity\Team;
use AppBundle\Entity\Racer;
use AppBundle\Entity\Pause;
use AppBundle\Entity\RacerPause;

use Faker;

class FakerFixturesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('faker:fixtures')
            ->setDescription('Generate fixtures')
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
            array(2, 0,  0, array()),
            array(5, 3,  2, array(array('00:00', '03:00'), array('03:00', '06:00'))),
            array(10, 5, 2, array(array('22:00', '03:00'), array('03:00', '08:00'))),
            array(10, 5, 2, array(array('22:00', '03:00'), array('03:00', '08:00'))),
            array(10, 5, 2, array(array('22:00', '03:00'), array('03:00', '08:00'))),
        );

        $pauseHeurePivot = new \Datetime('03:00');
        foreach($teamsTypes as $teamType) {
            $team = new Team;
            $name = sprintf('NR %s', $faker->city);
            $team
                ->setName($name)
                ->setNbHeurePause($teamType[1])
                ->setNbPerson($teamType[0])
                ;
            $em->persist($team);


            $pauseData = $teamType[3];
            if(!$pauseData)
            {
                // pas de pause de déclarée
                continue;
            }

            $teamPauses = array();

            foreach($pauseData as $i => $p)
            {
                $pause = new Pause;
                $pause
                    ->setPorder($i + 1)
                    ->setHourStart(new \Datetime($p[0]))
                    ->setHourStop(new \Datetime($p[1]))
                    ->setIdTeam($team)
                    ;

                $em->persist($pause);
                $teamPauses[] = $pause;
            }


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

                // fixme, only works with 2 pauses
                $pauseId = (($i+1) <= ($team->getNbPerson() / $teamType[2])) ? 0 : 1;

                $racerPause = new RacerPause;
                $racerPause
                    ->setPorder($i + 1)
                    ->setIdRacer($racer)
                    ->setIdPause($teamPauses[$pauseId])
                    ;
                $em->persist($racerPause);
            }


        }
        $em->flush();

    }
}
