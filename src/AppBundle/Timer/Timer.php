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

            if($nbTiming[1] == $teamStats->getTour()) {
                echo 'Bon nombre de tour: '.$teamStats->getTour();
                continue;
            }

            try {
                $latestTeamTiming = $repoTiming->getLatestTeamTiming($team, 1);
            // FIXME no result exception
            } catch(\Exception $e) {
                $latestTeamTiming = null;
            }

            $nextRacer = $this->guesser
                ->setTeam($team)
                ->getNext()
                ;

            $interval = \DateInterval::createFromDateString($teamStats->getTemps());
            $endLap = clone $race->getStart();
            $endLap->add($interval);

            if(!$latestTeamTiming) {
                $t = new \Datetime('today '.$teamStats->getTemps());
            } else {
                $t = null;
            }

            $timing = new Timing;
            $timing
                ->setCreatedAt(new \Datetime())
                ->setIdRacer($nextRacer)
                ->setIsRelay(0)
                ->setTiming(new \Datetime())
                ->setClock($endLap)
                ->setAutomatic()
                ;
            $this->em->persist($timing);

            $ranking = new Ranking;
            $ranking
                ->setBestLap(new \Datetime('today 00:'.$teamStats->getBestLap()))
                ->setCreatedAt($timing->getCreatedAt())
                ->setDistance($teamStats->getDistance())
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
