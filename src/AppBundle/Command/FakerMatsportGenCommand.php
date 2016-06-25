<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\NoResultException;

use AppBundle\Entity\Timing;

use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class FakerMatsportGenCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('faker:matsport:gen')
            ->setDescription('Faker for matsport stats generation')
            //->addArgument('team', InputArgument::REQUIRED, 'team to use')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $verbose = $input->getOption('verbose');
        $container = $this->getContainer();
        $rootDir = $container->getParameter('kernel.root_dir');
        $file = $rootDir . DIRECTORY_SEPARATOR . 'matsport.html';

        $twig = $container->get('twig');

        $template = $twig->loadTemplate('AppBundle:Matsport:main.html.twig');

        $data = array();

        $em = $container->get('doctrine')->getManager();

        $repoTeams = $em->getRepository('AppBundle:Team');
        $teams = $repoTeams->findAll();

        $race = $this->getContainer()->get('race')->get(); //$em->getRepository('AppBundle:Race')->find(1);

        $raceStart = $race->getStart();

        $repoMatsport = $em->getRepository('AppBundle:Matsport');
        $verbose && $output->writeln(sprintf('Iterating over %d teams ...', count($teams)));

        foreach($teams as $team) {
            try {
                $lastTiming = $repoMatsport->findLatestForTeam($team->getId());
            } catch(NoResultException $e) {
                //$output->writeln('No timing (yet) for '.$team->getName());
                continue;
            }
            $nbLap = $repoMatsport->nbLapForTeam($team->getId());
            $verbose && $output->writeln(sprintf('Team %s %s ran %d laps', $team->getId(), $team->getName(), $nbLap[1]));

            $time = $lastTiming->getClock()->diff($raceStart);
            $secs = $time->format('%H') * 3600 + $time->format('%I') * 60 + $time->format('%S');
            //$kmh = ((1 * ($nbLap[1] * $race->getKm())) / ($secs/3600));
            $kmh = (1 * $race->getKm()) / ($secs / 3600);

            //$elapsed = clone $lastTiming->getTiming();
            //$interval = \DateInterval::createFromDateString($elapsed->format('H:i:s'));
            //$elapsed->add($interval);
            $lastClock = clone $lastTiming->getClock();
            $verbose && $output->writeln('Lastclock: '.$lastClock->format('H:i:s'));

            $verbose && $output->writeln('Started at: '.$raceStart->format('H:i:s'));
            $interval = $raceStart->diff($lastClock);
            $elapsed = $interval->format('%H:%I:%S');
            $verbose && $output->writeln('elapsed: '.$elapsed);

            $data[$team->getId()] = array(
                'id' => $team->getId(),
                'pos' => rand(20, 100),
                'team' => $team,
                'timing' => $lastTiming,
                'laps' => $nbLap[1],
                'km' => $nbLap[1] * $race->getKm(),
                'type' => 'Prestige',
                //'time' => $elapsed->format('%H:%I:%S'),
                'time' => $elapsed,
                'vitesse' => round($kmh, 1),
                'ecart' => sprintf('%02d:%02d:%02d.%03d', rand(0,2), rand(0,59), rand(0,59), rand(0,999)),
                'bestlap' => sprintf('%02d:%02d.%03d', rand(0,59), rand(0,59), rand(0,999)),
                'poscat' => rand(15, 40),
            );
        }

        //$data = $twig->render($template, $data);
        $html = $template->render(array('entries' => $data));
        file_put_contents($file, $html);

        return 0;
    }
}
