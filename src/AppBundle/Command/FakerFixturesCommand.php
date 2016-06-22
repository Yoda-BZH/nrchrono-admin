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
use AppBundle\Entity\Race;

use Faker;

class FakerFixturesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('faker:fixtures')
            ->setDescription('Generate fixtures')
            //->addArgument('provider', InputArgument::REQUIRED, 'Provider to use')
            ->addArgument('timing', InputArgument::OPTIONAL, 'timing', 12)
            //->addOption('yell', null, InputOption::VALUE_NONE, 'Si définie, la tâche criera en majuscules')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $mediumTiming = $input->getArgument('timing');
        $em = $this->getContainer()->get('doctrine')->getManager();

        //$repoRanking = $em->getRepository('AppBundle:Ranking');

        $faker = Faker\Factory::create('fr_FR');

        $repoRacer = $em->getRepository('AppBundle:Racer');

        $race = new Race;
        $race
            ->setName('24H du Mans Roller 2016')
            ->setStart(new \Datetime)
            ->setKm('4.185')
            ;
        //$race = new Race;
        //$race
        //    ->setName('Groll Race 2016')
        //    ->setStart(new \Datetime('2016-06-18 15:00:00'))
        //    ->setKm('2.700')
        //    ;

        $em->persist($race);

        $today = date('Y-m-d');
        $tomorrow = date('Y-m-d', time() + 3600 * 24 + 1);

        $pausesTeam = array();
        $pausesTeam[10] = array(
            array(new \Datetime($today . ' 22:00'), new \Datetime($tomorrow . ' 03:00')),
            array(new \Datetime($tomorrow . ' 03:00'), new \Datetime($tomorrow . ' 08:00')),
        );
        $pausesTeam[6] = array(
            array(new \Datetime($tomorrow . ' 00:00'), new \Datetime($tomorrow . ' 03:00')),
            array(new \Datetime($tomorrow . ' 03:00'), new \Datetime($tomorrow . ' 06:00')),
        );
        $pausesTeam[5] = array(
            array(new \Datetime($tomorrow . ' 00:00'), new \Datetime($tomorrow . ' 03:00')),
            array(new \Datetime($tomorrow . ' 03:00'), new \Datetime($tomorrow . ' 06:00')),
        );
        $pausesTeam[2] = array(
            array(new \Datetime($today . ' 22:00'), new \Datetime($tomorrow . ' 00:00')),
            array(new \Datetime($tomorrow . ' 00:00'), new \Datetime($tomorrow . ' 02:00')),
            array(new \Datetime($tomorrow . ' 02:00'), new \Datetime($tomorrow . ' 04:00')),
            array(new \Datetime($tomorrow . ' 04:00'), new \Datetime($tomorrow . ' 06:00')),
        );

        if(is_file(__DIR__.'/fixtures.php')) {
            include __DIR__.'/fixtures.php';
        } else {
            $teamsTypes = array(
                array(2, 1,  1, $pausesTeam[2], '#c71e1e', sprintf('NR %s', $faker->city)),
                array(5, 3,  2, $pausesTeam[5], '#e2ff00', sprintf('NR %s', $faker->city)),
                array(10, 5, 5, $pausesTeam[10], '#3928c7', sprintf('NR %s', $faker->city)),
                array(10, 5, 5, $pausesTeam[10], '#10d21a', sprintf('NR %s', $faker->city)),
                array(10, 5, 5, $pausesTeam[10], '#c820be', sprintf('NR %s', $faker->city)),
            );
        }

        foreach($teamsTypes as $k => $teamType) {
            $team = new Team;
            $name = $teamType[5];
            $team
                ->setName($name)
                ->setNbHeurePause($teamType[1])
                ->setNbPerson($teamType[0])
                ->setIdRace($race)
                ->setColor($teamType[4])
                ->setIdReference($k + 1)
                ;
            $em->persist($team);
            $output->writeln('Adding team '.$team->getName());


            for($i = 0; $i < $teamType[0]; $i++) {
                $racer = new Racer;
                $tmin = new \Datetime('00:00:00');
                $tmax = new \Datetime('00:00:00');

                if($mediumTiming == 12) {
                    $tmin->modify(sprintf('+ %s seconds', $tmn = rand(9 * 60, 11 * 60)));
                    $tmax->modify(sprintf('+ %s seconds', $tmx = rand(11 * 60, 13 * 60)));
                } elseif($mediumTiming == 3) {
                    $tmin->modify(sprintf('+ %s seconds', $tmn = rand(2.5 * 60, 4 * 60)));
                    $tmax->modify(sprintf('+ %s seconds', $tmx = rand(3.5 * 60, 4.5 * 60)));
                } else {
                    throw new \Exception('Bad --timing');
                }
                $tavg = new \Datetime('00:00:00');
                $tavg->modify(sprintf('+ %d seconds', ($tmn + $tmx) / 2));

                if (isset($teamType[6])) {
                    $firstname = $teamType[6][$i][0];
                } else {
                    $firstname = $faker->firstname;
                }
                $racer
                    ->setFirstname($firstname)
                    ->setLastName('')
                    ->setNickname($firstname)
                    ->setPosition($i + 1)
                    ->setIdTeam($team)
                    ->setTimingAvg($tavg)
                    ->setTimingMin($tmin)
                    ->setTimingMax($tmax)
                    ;

                $output->writeln(sprintf('Adding %s in team %s', $racer->getNickname(), $team->getName()));
                $em->persist($racer);
            }
        }
        $output->writeln('Saving data ...');
        $em->flush();

        // add first predictions
        $repoTeam = $em->getRepository('AppBundle:Team');
        $teams = $repoTeam->findAll();

        $logger = $this->getContainer()->get('logger');
        $logger->info('starting initialize');
        foreach($teams as $team) {
            $nextGuesser = $this->getContainer()->get('racer.next');
            $nextRacers = $nextGuesser
                ->setTeam($team)
                ->setLogger($logger)
                ->initialize()
                ;
        }
        $logger->info('initialize done.');

        $output->writeln('done.');

        return 0;
    }
}
