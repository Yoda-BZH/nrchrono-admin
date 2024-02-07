<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

use App\Repository\TeamRepository;

use App\Service\NextRacerGuesser;
use App\Service\RaceManager;

use App\Entity\Team;


class TimingConsolidate
{

    public function __construct(
        private EntityManagerInterface $em,
        private NextRacerGuesser $nextRaceGuesser,
        private TeamRepository $teamRepository,
        private RaceManager $raceManager,
        private LoggerInterface $logger,
    )
    {
    }

    public function run(?Team $singleTeam = null): int
    {
        if (!$this->raceManager->isStarted())
        {
            //$this->logger->info('race has not started yet');

            return 0;
        }

        $teams = $singleTeam ? array($singleTeam) : $this->teamRepository->getAll();

        foreach($teams as $team)
        {
            //$this->logger->info(sprintf('------------- %s -------------', $team->getName()));
            $nextRacers = $this->nextRaceGuesser
                ->setTeam($team)
                ->getNexts()
                ;
            $predictions = $this->nextRaceGuesser
                ->getPredictions($team->getId())
                ;

            $teamId = $team->getId();

            foreach($predictions as $i => $prediction)
            {
                //$this->logger->info(sprintf('----------------- index %s ---------------', $i));
                if($i == 0)
                {
                    //$previousPrediction = clone $this->nextRaceGuesser->getLatestTiming[0]->getClock() ?: clone $this->raceManager->get()->getStart();
                    // race has started, someone made ONE lap
                    $teamLatestTimings = $this->nextRaceGuesser->getLatestTiming();
                    if($teamLatestTimings)
                    {
                        $previousPrediction = clone $teamLatestTimings->getClock();
                        //$this->logger->info(sprintf(
                        //    'Race has started, %s made one lap, arrived at %s, id %d',
                        //    (string) $teamLatestTimings->getRacer(),
                        //    $teamLatestTimings->getClock()->format('H:i:s'),
                        //    $teamLatestTimings->getId()
                        //));
                        //$this->logger->info(sprintf('prediction should be %d', $prediction->getId()));
                    }
                    else
                    {
                        // race has not started, or the first racer is still doing the very first lap
                        $previousPrediction = clone $this->raceManager->get()->getStart();
                        $previousPrediction->add(new \DateInterval($prediction->getRacer()->getTimingAvg()->format('\P\TH\Hi\Ms\S')));
                        //$this->logger->info(sprintf(
                        //    'Race has NOT started, using racer %s with average %s to arrive at %s',
                        //    (string) $prediction->getRacer(),
                        //    $prediction->getRacer()->getTimingAvg()->format('H:i:s'),
                        //    $previousPrediction->format('H:i:s'),
                        //));
                    }
                }
                else
                {
                    $previousPrediction = clone $predictions[$i - 1]->getClock();
                    //$this->logger->info(sprintf(
                    //    'Using previous prediction %d of %s arrived at %s',
                    //    $predictions[$i - 1]->getId(),
                    //    $predictions[$i - 1]->getRacer(),
                    //    $previousPrediction->format('H:i:s')
                    //));
                    //$this->logger->info(sprintf('treating current prediction %d', $prediction->getId()));
                }
                //$currentPrediction = $predictions[$i];

                $previousPrediction->add(new \DateInterval($prediction->getRacer()->getTimingAvg()->format('\P\TH\Hi\Ms\S')));
                //$this->logger->info(sprintf('Updating id %s for %s from %s to %s',
                //    $prediction->getId(),
                //    (string) $prediction->getRacer(),
                //    $prediction->getClock()->format('H:i:s'),
                //    $previousPrediction->format('H:i:s'),
                //));
                if($previousPrediction != $prediction->getClock())
                {
                    $prediction
                        ->setClock($previousPrediction)
                        ;
                    $this->em->persist($prediction);
                }
            }
        }
        $this->em->flush();

        return 0;
    }

}
