<?php

namespace AppBundle\Service;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;

class NextRacerGuesser {

    private $em;
    private $team;
    private $latestRacer;
    private $latestTiming;
    private $repoTiming = null;
    private $repoRacer = null;
    private $nextRacers = array();

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

        $this->repoTiming = $this->em->getRepository('AppBundle:Timing');

        $this->repoRacer = $this->em->getRepository('AppBundle:Racer');

        $this->repoPause = $this->em->getRepository('AppBundle:RacerPause');

        return $this;
    }

    public function setTeam($team) {
        $this->team = $team;

        return $this;
    }

    public function computeNexts() {
        $teamId = $this->team->getId();

        if (isset($this->nextRacers[$teamId]))
        {
            return $this->nextRacers;
        }

        try {
            $this->latestTiming[$teamId] = $this->repoTiming->getLatestTeamTiming($this->team->getId());
            $this->latestRacer[$teamId] = $this->latestTiming[$teamId]->getIdRacer();
            $position = $this->latestRacer[$teamId]->getPosition();
        } catch(NoResultException $e) {
            $this->latestRacer[$teamId] = $this->repoRacer->getFirstOfTeam($this->team);
            $this->latestTiming[$teamId] = null;
            // Fix, no racer is on track, so, really find the first one.
            $position = 0;
        }

        try {
            // first search for predictions
            $predictions = $this->repoTiming->getPredictionsForTeam($this->team, $position);

            // then search normal racers
            $this->nextRacers[$teamId] = $this->repoRacer->getAllRacersAvailable($this->team, $position);

            // fix the first racer has to do 2 laps in a row
            if ($this->latestTiming[$teamId] == null)
            {
                array_unshift($this->nextRacers[$teamId], $this->nextRacers[$teamId][0]);
            }

            // remplacing racers with predictions
            foreach($predictions as $index => $prediction) {
                //echo 'Setting '.$index.' to '.$prediction->getIdRacer()->getNickname();
                $this->nextRacers[$teamId][$index] = $prediction->getIdRacer();
            }
        } catch(NoResultException $e) {
            return null;
        }

        // pause managementstart
if(1) {
        $dt = new \Datetime();
        $previousRacer = $this->latestRacer[$teamId];
        //$hourPauses = array_keys($pauses);
        foreach($this->nextRacers[$teamId] as $index => $racer)
        {
            $dtCheck = clone $dt;
            //$dtCheck->modify(sprintf('+%d seconds', $previousRacer->getTimingAvg()));
            $dtCheck->add(new \DateInterval($previousRacer->getTimingAvg()->format('\P\TH\Hi\Ms\S')));
            $hourStart = $dtCheck->format('YmdHi');

            foreach($racer->getPauses() as $pause) {
                $pauseStart = $pause->getIdPause()->getHourStart()->format('YmdHi');
                $pauseStop  = $pause->getIdPause()->getHourStop()->format('YmdHi');

                //$hourStopFixed = ($pauseStop < $pauseStart) ? $pauseStop + 10000 : $pauseStop;
                /*echo sprintf('checking if %d > %d AND %d < %d <br />',
                    $hourStart,
                    $pauseStart,
                    $hourStart,
                    $pauseStop
                ).PHP_EOL;*/
                if($hourStart > $pauseStart && $hourStart < $pauseStop) {
                    /*echo sprintf('stopping as %d > %d AND %d < %d <br />',
                        $hourStart,
                        $pauseStart,
                        $hourStart,
                        $pauseStop
                    ).PHP_EOL;*/
                    // remove racer as it's pause time
                    unset($this->nextRacers[$teamId][$index]);
                    continue;
                }
            }
            //foreach($racers as $racer) {
            //    $dt->add(new \DateInterval($previousRacer->getTimingAvg()->format('\P\TH\Hi\Ms\S')));
            //    $d = clone $dt;
            //
            //    $nextRacers[] = array(
            //        'racer' => $racer,
            //        'hour'  => $d->format('H:i'),
            //    );
            //    $previousRacer = $racer;
            //}
            $previousRacer = $racer;

        }
        // pause management stop
        $this->nextRacers[$teamId] = array_values($this->nextRacers[$teamId]);
}
        return $this->nextRacers[$teamId];
    }

    public function getNexts() {
        return $this->nextRacers[$this->team->getId()] = $this->computeNexts();
    }

    public function getNext() {
        $this->nextRacers[$this->team->getId()] = $this->computeNexts();

        return $this->nextRacers[$this->team->getId()][0];
    }

    public function getLatest() {
        return $this->latestRacer[$this->team->getId()];
    }

    public function getLatestTiming() {
        return $this->latestTiming[$this->team->getId()];
    }
}
