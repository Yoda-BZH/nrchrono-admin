<?php

namespace AppBundle\Service;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;

class NextRacerGuesser {

    private $em;
    private $team;
    private $latestRacer;
    private $latestTiming;

    public function __construct() {

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

    public function setTeam($team) {
        $this->team = $team;

        return $this;
    }

    public function computeNexts() {
        $repoTiming = $this->em->getRepository('AppBundle:Timing');

        try {
            //$this->latestRacer = $repoTiming->getLatestRacer($this->team->getId());
            $this->latestTiming = $repoTiming->getLatestTeamTiming($this->team->getId());
            $this->latestRacer = $this->latestTiming->getIdRacer();
        } catch(NoResultException $e) {
            $this->latestTiming = null;
            $this->latestRacer = null;
            return null;
        }

        $position = $this->latestRacer->getPosition();

        $repoRacer = $this->em->getRepository('AppBundle:Racer');
        try {
            $nextRacers = $repoRacer->getAllRacersAvailable($this->team, $position);
        } catch(NoResultException $e) {
            return null;
        }

        return $nextRacers;
    }

    public function getNexts() {
        return $this->computeNexts();
    }

    public function getNext() {
        $nextRacers = $this->computeNexts();

        return $nextRacers[0];
    }

    public function getLatest() {
        return $this->latestRacer;
    }

    public function getLatestTiming() {
        return $this->latestTiming;
    }
}
