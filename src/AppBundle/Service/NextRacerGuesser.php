<?php

namespace AppBundle\Service;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use AppBundle\Entity\Timing;

use Symfony\Component\HttpKernel\Log\NullLogger;

class NextRacerGuesser {

    const MAX_PREDICTION = 12; // doit être plus grand de 1 que le nombre max de racer dans une team

    private $em;
    private $logger;
    private $team;
    private $latestRacer;
    private $latestTiming;
    private $repoTiming = null;
    private $repoRacer = null;
    private $nextRacers = array();
    private $predictions = array();

    public function __construct() {
        $this->logger = new NullLogger();
    }

    private function isFirstLap($teamId)
    {
        return $this->latestTiming[$teamId] == null;
    }

    private function firstRacerRunsTwoLaps($teamId)
    {
        $this->logger->info(sprintf('Defining first two laps for %s', $this->nextRacers[$teamId][0]->getNickname()));
        array_unshift($this->nextRacers[$teamId], $this->nextRacers[$teamId][0]);
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

    public function setLogger($logger)
    {
        $this->logger = $logger;

        return $this;
    }

    public function setTeam($team) {
        $this->team = $team;

        return $this;
    }

    public function initialize()
    {
        return $this->computeNexts(true);
    }

    public function computeNexts($fixFirstLap = false) {
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

        //try {
            // first search for predictions
            $predictions = $this->repoTiming->getPredictionsForTeam($this->team, $position);

            $nbPrediction = count($predictions);
            $this->logger->info(sprintf('racer.next: for team %d, found %d predictions', $teamId, $nbPrediction));

            // then search normal racers
            $this->nextRacers[$teamId] = $this->repoRacer->getAllRacersAvailable($this->team, $position);

            //$nbRacerInTeam = count($this->nextRacers[$teamId]);
            //if($nbRacerInTeam < self::MAX_PREDICTION)
            //{
            //    $nbToFill = floor(self::MAX_PREDICTION / $nbRacerInTeam);
            //    $newNextRacers = array();
            //    for($n = 0; $n < $nbToFill; $n++)
            //    {
            //        //$newNextRacers = array_merge($newNextRacers, $this->nextRacers[$teamId]);
            //        $newNextRacers[$team][] = $this->repoRacer->getNextRacerAvailableQuery($this->team,
            //    }
            //    $this->nextRacers[$teamId] = $newNextRacers;
            //}

            // remplacing racers with predictions
            foreach($predictions as $index => $prediction) {
                //echo 'Setting '.$index.' to '.$prediction->getIdRacer()->getNickname();
                //$prediction->getIdRacer()->setCurrentPrediction($prediction);
                $this->predictions[$teamId][$index] = $prediction;
                $this->nextRacers[$teamId][$index] = $prediction->getIdRacer();
            }

            // fix the first racer has to do 2 laps in a row
            if ($fixFirstLap) // && $this->isFirstLap($teamId) && $nbPrediction == 0)
            {
                $this->firstRacerRunsTwoLaps($teamId);
            }

            //if($nbPrediction < self::MAX_PREDICTION)
            //{
            $this->logger->info(sprintf('checking for missing predictions (%d < %d)', $nbPrediction, self::MAX_PREDICTION));
            for($i = 0; $i < self::MAX_PREDICTION; $i++)
            {
                if(isset($this->predictions[$teamId][$i])) {
                    continue;
                }

                if(isset($this->nextRacers[$teamId][$i])) {
                    $currentRacer = $this->nextRacers[$teamId][$i];
                    $this->logger->info(sprintf('found current racer "%s" without prediction', $currentRacer->getNickname()));
                } else {
                    $previousRacer = $this->nextRacers[$teamId][$i - 1];
                    $currentRacer = $this->repoRacer->getNextRacerAvailable($this->team, $previousRacer->getPosition());
                    $this->logger->info(sprintf('could not find current racer, with "%s", determined to be "%s" next', $previousRacer->getNickname(), $currentRacer->getNickname()));
                }

                $timing = new Timing();
                $timing
                    ->setCreatedAt(new \Datetime)
                    ->setIdRacer($currentRacer)
                    ->setIsRelay(0)
                    ->setPrediction()
                    ;
                $this->em->persist($timing);

                $this->predictions[$teamId][$i] = $timing;
                $this->nextRacers[$teamId][$i] = $timing->getIdRacer();
                $this->logger->info(sprintf('For team %d, adding new timing for %s at index %d', $teamId, $timing->getIdRacer()->getNickname(), $i));
            }
            $this->em->flush();
            //}

        //} catch(NoResultException $e) {
        //    return null;
        //}

        //// pause managementstart
        //
        //$dt = new \Datetime();
        //$previousRacer = $this->latestRacer[$teamId];
        ////$hourPauses = array_keys($pauses);
        //foreach($this->nextRacers[$teamId] as $index => $racer)
        //{
        //    $dtCheck = clone $dt;
        //    //$dtCheck->modify(sprintf('+%d seconds', $previousRacer->getTimingAvg()));
        //    $dtCheck->add(new \DateInterval($previousRacer->getTimingAvg()->format('\P\TH\Hi\Ms\S')));
        //    $hourStart = $dtCheck->format('YmdHi');
        //
        //    foreach($racer->getPauses() as $pause) {
        //        $pauseStart = $pause->getIdPause()->getHourStart()->format('YmdHi');
        //        $pauseStop  = $pause->getIdPause()->getHourStop()->format('YmdHi');
        //
        //        //$hourStopFixed = ($pauseStop < $pauseStart) ? $pauseStop + 10000 : $pauseStop;
        //        /*echo sprintf('checking if %d > %d AND %d < %d <br />',
        //            $hourStart,
        //            $pauseStart,
        //            $hourStart,
        //            $pauseStop
        //        ).PHP_EOL;*/
        //        if($hourStart > $pauseStart && $hourStart < $pauseStop) {
        //            /*echo sprintf('stopping as %d > %d AND %d < %d <br />',
        //                $hourStart,
        //                $pauseStart,
        //                $hourStart,
        //                $pauseStop
        //            ).PHP_EOL;*/
        //            // remove racer as it's pause time
        //            unset($this->nextRacers[$teamId][$index]);
        //            continue;
        //        }
        //    }
        //    //foreach($racers as $racer) {
        //    //    $dt->add(new \DateInterval($previousRacer->getTimingAvg()->format('\P\TH\Hi\Ms\S')));
        //    //    $d = clone $dt;
        //    //
        //    //    $nextRacers[] = array(
        //    //        'racer' => $racer,
        //    //        'hour'  => $d->format('H:i'),
        //    //    );
        //    //    $previousRacer = $racer;
        //    //}
        //    $previousRacer = $racer;
        //
        //}
        //// pause management stop
        $this->nextRacers[$teamId] = array_values($this->nextRacers[$teamId]);

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

    public function getPredictions($teamId)
    {
        return $this->predictions[$teamId];
    }
}
