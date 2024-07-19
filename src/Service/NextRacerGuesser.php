<?php

namespace App\Service;
#use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use App\Entity\Timing;
Use App\Repository\TimingRepository;
Use App\Repository\RacerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Stopwatch\Stopwatch;


use Psr\Log\LoggerInterface;

class NextRacerGuesser {

    const MAX_PREDICTION = 5; // doit être plus grand de 1 que le nombre max de racer dans une team

    #private $logger;
    private $team;
    private $latestRacer;
    private $latestTiming;
    private $nextRacers = array();
    private $predictions = array();

    public function __construct(
        private LoggerInterface $logger,
        private TimingRepository $timingRepository,
        private RacerRepository $racerRepository,
        private EntityManagerInterface $em,
        private RaceManager $raceManager,
        private Stopwatch $stopwatch,
    ) {
        //$this->logger = new NullLogger();
    }

    private function isFirstLap($teamId)
    {
        return $this->latestTiming[$teamId] == null;
    }

    private function firstRacerRunsTwoLaps($teamId)
    {
        $nextRacers[$teamId] = $this->racerRepository->getAllRacersAvailable($this->team, 0);
        $this->logger->info(sprintf('Defining first two laps for %s', $nextRacers[$teamId][0]->getNickname()));
        array_unshift($this->nextRacers[$teamId], $nextRacers[$teamId][0]);
        array_unshift($this->nextRacers[$teamId], $nextRacers[$teamId][0]);
    }

    public function setTeam($team): static
    {
        $this->team = $team;

        return $this;
    }

    public function initialize(): array
    {
        return $this->computeNexts(true);
    }

    public function computeNexts($fixFirstLap = false): array
    {
        $this->stopwatch->start('next-race-guesser-compute');
        $this->logger->info(sprintf('Computing next predictions, with first lap: %d', $fixFirstLap));
        $teamId = $this->team->getId();

        if (isset($this->nextRacers[$teamId]))
        {
            $this->logger->info('returning from cache for team ' . $teamId);
            return $this->nextRacers;
        }

        try {
            $this->latestTiming[$teamId] = $this->timingRepository->getLatestTeamTiming($this->team->getId());
            $this->latestRacer[$teamId] = $this->latestTiming[$teamId]->getRacer();
            $position = $this->latestRacer[$teamId]->getPosition();
        }
        catch(NoResultException $e)
        {
            $this->latestRacer[$teamId] = $this->racerRepository->getFirstOfTeam($this->team);
            $this->latestTiming[$teamId] = null;
            // Fix, no racer is on track, so, really find the first one.
            $position = 0;
        }

        //try {
            // first search for predictions
            $predictions = $this->timingRepository->getPredictionsForTeam($this->team, $position);

            $nbPrediction = \count($predictions);
            $this->logger->info(sprintf('racer.next: for team %d, found %d predictions', $teamId, $nbPrediction));

            // then search normal racers
            //$this->nextRacers[$teamId] = $this->racerRepository->getAllRacersAvailable($this->team, $position);
            $this->nextRacers[$teamId] = array(); //$this->racerRepository->getAllRacersAvailable($this->team, $position);

            // remplacing racers with predictions
            foreach($predictions as $index => $prediction)
            {
                $this->predictions[$teamId][$index] = $prediction;
                $this->nextRacers[$teamId][$index] = $prediction->getRacer();
            }

            // fix the first racer has to do 2 laps in a row
            if ($fixFirstLap or !$this->nextRacers[$teamId])
            {
                $this->firstRacerRunsTwoLaps($teamId);
            }

            $this->logger->info(sprintf('checking for missing predictions (%d < %d)', $nbPrediction, self::MAX_PREDICTION));
            for($i = 0; $i < self::MAX_PREDICTION; $i++)
            {
                if(isset($this->predictions[$teamId][$i]))
                {
                    continue;
                }

                if(0 == $i)
                {
                    //$clock = new \Datetime();
                    $clock = clone $this->raceManager->get()->getStart();
                }
                else
                {
			if (!isset($this->predictions[$teamId][$i - 1]))
			{
				$this->logger->info(sprintf('error, no previous racer found for team %d for $i', $teamId, $i));
				continue;
			}
                    $clock = clone $this->predictions[$teamId][$i - 1]->getClock();
                }

                if(isset($this->nextRacers[$teamId][$i]))
                {
                    $currentRacer = $this->nextRacers[$teamId][$i];
                    $this->logger->info(sprintf('found current racer "%s" without prediction', $currentRacer->getNickname()));
                }
                else
                {
                    $currentRacer = null;
                    $iterations = (int) -1;
                    do {
                        $iterations++;
                        $this->logger->info(sprintf('trying iteration %d', $iterations));
                        $previousRacer = $this->nextRacers[$teamId][$i - 1];
                        $isCurrentRacer = $this->racerRepository->getNextRacerAvailable($this->team, $previousRacer->getPosition() + $iterations);
                        //$isCurrentRacer = $this->racerRepository->getNextRacerAvailable($this->team, $previousRacer->getPosition() + $iterations % $this->team->getNbPerson());
			$this->logger->info(sprintf('for team %d get next racer %s as pos %d and iter %d', $teamId, (string) $isCurrentRacer, $previousRacer->getPosition(), $iterations));
                        $racerPauses = $isCurrentRacer->getRacerPauses();
                        if (!\count($racerPauses))
                        {
                            $this->logger->info(sprintf('found current that fits: %s, had no pause scheduled', (string) $isCurrentRacer));
                            $currentRacer = $isCurrentRacer;
                            break;
                        }
                        else
                        {
                            $this->logger->info(sprintf(
                                'racer %s has %d racerpauses', (string) $isCurrentRacer, count($racerPauses)
                            ));
                        }
                        foreach($racerPauses as $racerPause)
                        {
                            $pause = $racerPause->getPause();
                            if ($pause->getHourStart() < $clock && $pause->getHourStop() > $clock)
                            {
                                $this->logger->info(
                                    sprintf(
                                        'Was checking for %s, but it seems it\'s paused at %s, between %s and %s',
                                        $isCurrentRacer->getNickname(),
                                        $clock->format('c'),
                                        $pause->getHourStart()->format('c'),
                                        $pause->getHourStop()->format('c')
                                    )
                                );
                                continue;
                            }
                            else
                            {
                                $this->logger->info(sprintf(
                                    'found %s', $isCurrentRacer
                                ));
                                $currentRacer = $isCurrentRacer;
                                break;
                            }
                        }
                    } while(!$currentRacer && $iterations < 30);

                    //$this->logger->info(sprintf('could not find current racer, with "%s", determined to be "%s" next', $previousRacer->getNickname(), $currentRacer->getNickname()));
                }
		if (!$currentRacer)
		{
			$this->logger->error(sprintf('Cannot find another raceri for team %d, found null after %d iteration', $teamId, $iterations));
			$personne = $this->racerRepository->getPersonne($teamId);
			if($personne)
			{
				$currentRacer = $personne;
			}
			else
			{
				continue;
			}
		}

                $timing = new Timing();
                $created = new \Datetime();
                $clock->add(new \DateInterval($currentRacer->getTimingAvg()->format('\P\TH\Hi\Ms\S')));
                $timing
                    ->setCreatedAt($created)
                    ->setRacer($currentRacer)
                    ->setClock($clock)
                    ->setIsRelay(0)
                    ->setPrediction()
                    ;
                $this->em->persist($timing);

                $this->predictions[$teamId][$i] = $timing;
                $this->nextRacers[$teamId][$i] = $timing->getRacer();
                $this->logger->info(sprintf('For team %d, adding new timing for %s at index %d', $teamId, $timing->getRacer()->getNickname(), $i));
            }
            $this->em->flush();

        //} catch(NoResultException $e) {
        //    return null;
        //}
        $this->nextRacers[$teamId] = array_values($this->nextRacers[$teamId]);
        $this->stopwatch->stop('next-race-guesser-compute');

        return $this->nextRacers[$teamId];
    }

    public function getNexts($limit = -1): array
    {
        $this->computeNexts();
        if ($limit > 0)
        {
            return \array_slice($this->nextRacers[$this->team->getId()], 0, $limit);
        }

        return $this->nextRacers[$this->team->getId()];
    }

    public function getNext()
    {
        $this->computeNexts();

        return $this->nextRacers[$this->team->getId()][0];
    }

    public function getLatest()
    {
        return $this->latestRacer[$this->team->getId()];
    }

    public function getLatestTiming()
    {
        return $this->latestTiming[$this->team->getId()];
    }

    public function getPredictions($teamId)
    {
        return $this->predictions[$teamId];
    }
}
