<?php

namespace AppBundle\Timer;

use AppBundle\Timer\Provider\Provider;
use Doctrine\ORM\EntityManager;

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
                //$this->output->writeln(sprintf('Count of timings equals the number of laps done (%d)', $nbTiming[1]));
                continue;
            }

            try {
                $latestTeamTiming = $repoTiming->getLatestTeamTiming($team, 1);
                $this->output->writeln('Got latest team timing');
            // FIXME no result exception
            } catch(\Exception $e) {
                $latestTeamTiming = null;
                $this->output->writeln('No latest team timing ?');
            }
            $intervalPieces = explode(':', $teamStats->getTemps());
            $intervalStr = sprintf('PT%02dH%02dM%02dS',
                $intervalPieces[0],
                $intervalPieces[1],
                $intervalPieces[2]
            );

            $interval = new \DateInterval($intervalStr);
            $this->output->writeln('Got Temps to '.$teamStats->getTemps());
            $endLap = clone $race->getStart();
            $this->output->writeln('Race start is '.$endLap->format('H:i:s'));
            $endLap->add($interval);
            $this->output->writeln(sprintf('Adding interval %s to endlap', $interval->format('%H:%I:%S')));
            $this->output->writeln('Endlap is now '.$endLap->format('H:i:s'));

            if(!$latestTeamTiming) {
                $t = new \Datetime('today '.$teamStats->getTemps());
            } else {
                //$lastTiming = clone $latestTeamTiming->getTiming();
                //$t->add($interval);
                $dtTemps = new \Datetime('today '.$teamStats->getTemps());
                $t = $dtTemps->sub(new \DateInterval($latestTeamTiming->getTiming()->format('\P\TH\Hi\Ms\S')));
                $this->output->writeln('Seems the last lap was '.$t->format('H:i:s'));
                //$t = new \Datetime('today '.$tInterval->format('%H:%M:%S'));
            }

            $nextRacer = $this->guesser
                ->setTeam($team)
                ->getNext()
                ;

            $timing = new Timing;
            $timing
                ->setCreatedAt(new \Datetime())
                ->setIdRacer($nextRacer)
                ->setIsRelay(0)
                ->setTiming($t)
                ->setClock($endLap)
                ->setAutomatic()
                ;
            $this->em->persist($timing);

            $ranking = new Ranking;
            $ranking
                ->setBestLap(new \Datetime('today 00:'.$teamStats->getBestLap()))
                ->setCreatedAt($timing->getCreatedAt())
                ->setDistance($teamStats->getDistance() * 1000)
                ->setEcart(new \Datetime('today '.$teamStats->getEcart()))
                ->setPoscat($teamStats->getPoscat())
                ->setPosition($teamStats->getPosition())
                ->setSpeed($teamStats->getVitesse())
                ->setTime(new \Datetime('today '.$teamStats->getTemps()))
                ->setTour($teamStats->getTour())
                ;
            $this->em->persist($ranking);
        }
        $this->em->flush();

        return 0;
    }

}
