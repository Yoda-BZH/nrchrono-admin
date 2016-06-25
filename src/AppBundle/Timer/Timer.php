<?php

namespace AppBundle\Timer;

use AppBundle\Timer\Provider\Provider;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;

use AppBundle\Entity\Timing;
use AppBundle\Entity\Ranking;

class Timer {

    private $provider;

    private $em;

    private $guesser;

    /**
     * description
     *
     * @param void
     * @return void
     */
    public function __construct()
    {

    }

    public function setIo($input, $output)
    {
        $this->input = $input;
        $this->output = $output;

        return $this;
    }

    /**
     * description
     *
     * @param void
     * @return void
     */
    public function setProvider(Provider $provider)
    {
        $this->provider = $provider;

        return $this;
    }

    /**
     * Set the value of
     *
     *
     */
    public function setEm(EntityManager $em)
    {
        $this->em = $em;

        return $this;
    }

    public function setGuesser($guesser)
    {
        $this->guesser = $guesser;

        return $this;
    }


    /**
     * description
     *
     * @param void
     * @return void
     */
    public function run()
    {
        $teamsStats = $this->provider->getGeneral();

        $repoTeam = $this->em->getRepository('AppBundle:Team');
        $repoTiming = $this->em->getRepository('AppBundle:Timing');
        //var_dump($teamsStats);

        $repoRace = $this->em->getRepository('AppBundle:Race');
        $race = $repoRace->find(1); // FIXME
        $now = new \Datetime();

        //var_dump($r);
        foreach($teamsStats as $teamStats) {
            $team = $repoTeam->findOneBy(array('idReference' => $teamStats->getNumero()));
            if(!$team)
            {
                throw new \Exception('Impossible de trouver la team ayant pour numéro '.$teamStats->getNumero());
            }
            //var_dump($team->getName());
            $nbTiming = $repoTiming->getNbForTeam($team);

            if($nbTiming[1] >= $teamStats->getTour()) {
                //echo 'Bon nombre de tour: '.$teamStats->getTour();
                //$this->output->writeln(sprintf('Count of timings equals the number of laps done (manually: %d >= matsport: %d ) %s', $nbTiming[1], $teamStats->getTour(), $team->getName()));
                continue;
            }
            echo 'foo';
            $this->output->writeln('Continuing team from matsport - ' . $team->getName());

            try {
                $latestTeamTiming = $repoTiming->getLatestTeamTiming($team, 1);
                $this->output->writeln('Got latest team timing for team ' . $team->getName());
            // FIXME no result exception
            } catch(NoResultException $e) {
                $latestTeamTiming = null;
                $this->output->writeln('No latest team timing ? ' . $team->getName());
            }
            $intervalPieces = explode(':', $teamStats->getTemps());
            $intervalStr = sprintf('PT%02dH%02dM%02dS',
                $intervalPieces[0],
                $intervalPieces[1],
                $intervalPieces[2]
            );

            $interval = new \DateInterval($intervalStr);
            //$this->output->writeln('Got Temps to '.$teamStats->getTemps());
            $endLap = clone $race->getStart();
            //$this->output->writeln('Race start is '.$endLap->format('H:i:s'));
            $endLap->add($interval);
            //$this->output->writeln(sprintf('Adding interval %s to endlap', $interval->format('%H:%I:%S')));
            //$this->output->writeln('Endlap is now '.$endLap->format('H:i:s'));
            $this->output->writeln('checking last timing for '.$team->getName());
            if(!$latestTeamTiming) {
                $t = new \Datetime('today '.$teamStats->getTemps());
            } else {
                $intervalTemps = $endLap->diff($latestTeamTiming->getClock());
                $t = new \Datetime('today '.$intervalTemps->format('%H:%I:%S'));
            }
            $this->output->writeln('getting nexdt guesser for '.$team->getName());
            $nextRacer = $this->guesser
                ->setTeam($team)
                ->getNext()
                ;

            $timing = new Timing;
            $timing
                ->setCreatedAt($now)
                ->setIdRacer($nextRacer)
                ->setIsRelay(0)
                ->setTiming($t)
                ->setClock($endLap)
                ->setAutomatic()
                ;
            $this->em->persist($timing);
            $this->output->writeln('saving new timing for team '.$team->getName());

            $ranking = new Ranking;
            $ranking
                ->setBestLap(new \Datetime('today 00:'.$teamStats->getBestLap()))
                ->setCreatedAt($now)
                ->setDistance($teamStats->getDistance() * 1000)
                ->setEcart($teamStats->getEcart())
                ->setPoscat($teamStats->getPoscat())
                ->setPosition($teamStats->getPosition())
                ->setSpeed($teamStats->getVitesse() * 1000)
                ->setTime(new \Datetime('today '.$teamStats->getTemps()))
                ->setTour($teamStats->getTour())
                ->setIdTeam($team)
                ;
            $this->em->persist($ranking);
            $this->output->writeln('saving new ranking for team '.$team->getName());
        }
        $this->em->flush();

        return 0;
    }

}
