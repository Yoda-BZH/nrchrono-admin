<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Team;
use App\Entity\Racer;
use App\Entity\Pause;
use App\Entity\RacerPause;
use App\Entity\Race;
use App\Repository\TeamRepository;

use App\Service\NextRacerGuesser;

use Faker;

#[AsCommand(
    name: 'faker:fixtures',
    description: 'Generate fixtures',
    hidden: false,
    aliases: []
)]
class FakerFixturesCommand extends Command
{
    public function __construct(
        private TeamRepository $teamRepository,
        private LoggerInterface $logger,
        private NextRacerGuesser $nextGuesser,
        private EntityManagerInterface $em
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Generate fixtures')
            //->addArgument('provider', InputArgument::REQUIRED, 'Provider to use')
            ->addArgument('timing', InputArgument::OPTIONAL, 'timing', 12)
            //->addOption('yell', null, InputOption::VALUE_NONE, 'Si définie, la tâche criera en majuscules')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $mediumTiming = $input->getArgument('timing');

        $faker = Faker\Factory::create('fr_FR');

        $race = new Race;
        $race
            ->setName('24H du Mans Roller 2024')
            //->setStart(new \Datetime("now + 5 minutes"))
            ->setStart(new \Datetime("2024-06-29 16:15:00"))
            ->setKm('4.185')
            ;
        //$race = new Race;
        //$race
        //    ->setName('Groll Race 2016')
        //    ->setStart(new \Datetime('2016-06-18 15:00:00'))
        //    ->setKm('2.700')
        //    ;

        $this->em->persist($race);

        $today = date('Y-m-d');
        $tomorrow = date('Y-m-d', time() + 3600 * 24 + 1);

        include __DIR__.'/fixtures.db';

        foreach($teamsTypes as $k => $teamType)
        {
            $team = new Team;
            $name = $teamType[2];
            $randMin = 100 * (count($teamType[3]) - 1);
            $randMax = $randMin + 99;
	    if(isset($teamType['id']))
	    {
		    $idEquipe = $teamType['id'];
	    }
	    else
	    {
		    $idEquipe = rand($randMin, $randMax);
	    }
            $team
                ->setName($name)
                ->setNbPerson($teamType[0])
                ->setRace($race)
                ->setColor($teamType[1])
                ->setIdReference($idEquipe)
                ->setGuest($teamType[4])
                ;
            $this->em->persist($team);
            $output->writeln('Adding team '.$team->getName());


            //for($i = 0; $i < $teamType[0]; $i++) {
            foreach($teamType[3] as $i => $racerInfos)
            {
                $racer = new Racer;
		if(isset($racerInfos['t']))
		{
			$tavg = new \Datetime('00:00:00');
			$t = $racerInfos['t'][0] * 60 + $racerInfos['t'][1];
			$tavg->modify(sprintf('+ %d seconds', $t));
			$tmin = clone $tavg;
			$tmax = clone $tavg;
		}
		else
		{
			$tavg = new \Datetime('00:00:00');
			$t = 10 * 60;
			$tavg->modify(sprintf('+ %d seconds', $t));
			$tmin = clone $tavg;
			$tmax = clone $tavg;
//			$tmin = new \Datetime('00:00:00');
//			$tmax = new \Datetime('00:00:00');
//
//			if($mediumTiming == 12)
//			{
//			    $tmin->modify(sprintf('+ %s seconds', $tmn = rand(9 * 60, 11 * 60)));
//			    $tmax->modify(sprintf('+ %s seconds', $tmx = rand(11 * 60, 13 * 60)));
//			}
//			elseif($mediumTiming == 3)
//			{
//			    $tmin->modify(sprintf('+ %s seconds', $tmn = rand(2.5 * 60, 4 * 60)));
//			    $tmax->modify(sprintf('+ %s seconds', $tmx = rand(3.5 * 60, 4.5 * 60)));
//			}
//			elseif($mediumTiming == 8)
//			{
//			    $tmin->modify(sprintf('+ %s seconds', $tmn = rand(6.5 * 60, 9 * 60)));
//			    $tmax->modify(sprintf('+ %s seconds', $tmx = rand(7.5 * 60, 10.5 * 60)));
//			}
//			else
//			{
//			    throw new \Exception('Bad --timing');
//			}
//			$tavg = new \Datetime('00:00:00');
//			$tavg->modify(sprintf('+ %d seconds', ($tmn + $tmx) / 2));
		}

                if (isset($teamType[3]))
                {
                    $firstname = $racerInfos[0];
                }
                else
                {
                    $firstname = $faker->firstname;
                }

                $racer
                    ->setFirstname($firstname)
                    ->setLastName('')
                    ->setNickname($firstname)
                    ->setPosition($i + 1)
                    ->setTeam($team)
                    ->setTimingAvg($tavg)
                    ->setTimingMin($tmin)
                    ->setTimingMax($tmax)
                    ;
                if ($firstname == '< Personne >')
                {
                    $racer->setPaused(true);
                }

                $output->writeln(sprintf('Adding %s in team %s', $racer->getNickname(), $team->getName()));
                $this->em->persist($racer);
            }
        }
        $output->writeln('Saving data ...');
        $this->em->flush();

        // add first predictions
        $teams = $this->teamRepository->findAll();

        $this->logger->info('starting initialize');
        foreach($teams as $team)
        {
            //$nextGuesser = $this->getContainer()->get('racer.next');
            $nextRacers = $this->nextGuesser
                ->setTeam($team)
                ->initialize()
                ;
        }
        $this->logger->info('initialize done.');

        $output->writeln('done.');

        return Command::SUCCESS;
    }
}
