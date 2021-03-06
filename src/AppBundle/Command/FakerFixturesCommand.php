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

        //$repoRacer = $em->getRepository('AppBundle:Racer');

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

        include __DIR__.'/fixtures.php';

        foreach($teamsTypes as $k => $teamType) {
            $team = new Team;
            $name = $teamType[2];
            $team
                ->setName($name)
                ->setNbHeurePause(0)
                ->setNbPerson($teamType[0])
                ->setIdRace($race)
                ->setColor($teamType[1])
                ->setIdReference($k + 1)
                ->setGuest($teamType[4])
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
                } elseif($mediumTiming == 8) {
                    $tmin->modify(sprintf('+ %s seconds', $tmn = rand(6.5 * 60, 9 * 60)));
                    $tmax->modify(sprintf('+ %s seconds', $tmx = rand(7.5 * 60, 10.5 * 60)));
                } else {
                    throw new \Exception('Bad --timing');
                }
                $tavg = new \Datetime('00:00:00');
                $tavg->modify(sprintf('+ %d seconds', ($tmn + $tmx) / 2));

                if (isset($teamType[3])) {
                    $firstname = $teamType[3][$i][0];
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
