<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Service\NextRacerGuesser;
use App\Service\RaceManager;

use App\Repository\TeamRepository;
use App\Repository\TimingRepository;

use Doctrine\ORM\NoResultException;

#[Route("/statistics")]
class StatisticsController extends AbstractController
{
    #[Route('/', name: 'app_statistics')]
    public function index(): Response
    {
        return $this->render('statistics/index.html.twig', [
            'controller_name' => 'StatisticsController',
        ]);
    }

    #[Route("/arrivals", name: "stats_arrivals", methods: ['GET'])]
    public function arrivals(
        NextRacerGuesser $nextGuesser,
        RaceManager $raceManager,
        TeamRepository $teamRepository,
    ): Response
    {

        $raceIsStarted = $raceManager->isStarted();

        if (!$raceIsStarted)
        {
            return $this->render('statistics/arrivals.html.twig', array(
                'arrivals' => array(),
            ));
        }

        $teams = $teamRepository->findAll();
        $now = new \Datetime();

        $arrivals = array();

        foreach($teams as $team)
        {

            $nextRacer = $nextGuesser
                ->setTeam($team)
                ->getNext()
                ;

            $latestRacer = $nextGuesser->getLatest();

            try {
                $latestTeamTiming = $nextGuesser->getLatestTiming();
                if(!$latestTeamTiming) {
                    throw new \Exception();
                }
                $clock = clone $latestTeamTiming->getClock();
            } catch(\Exception $e) {
                $race = $raceManager->get();
                $clock = clone $race->getStart();
            }

            $arrival = clone $clock;
            $interval = new \DateInterval($nextRacer->getTimingAvg()->format('\P\TH\Hi\Ms\S'));
            $arrival->add($interval);

            $arrivals[] = array("team" => $team, "racer" => $nextRacer, "arrival" => $arrival);

        }

        return $this->render('statistics/arrivals.html.twig', array(
            'arrivals' => $arrivals,
        ));
    }


    #[Route("/best-laps", name: "stats_best_laps", methods: ['GET'])]
    public function bestLaps(
        RaceManager $raceManager,
        TeamRepository $teamRepository,
        TimingRepository $timingRepository,
    ): Response
    {
        //$raceManager = $this->getContainer()->get('race');
        $raceIsStarted = $raceManager->isStarted();

        if (!$raceIsStarted)
        {
            return $this->render('statistics/bestlaps.html.twig', array());
        }

        $data = array();

        foreach($teamRepository->findAll() as $team)
        {
            try {
                $timing = $timingRepository->getBestTeamLap($team);
            } catch(NoResultException $e) {
                $data[] = array(
                    'label' => sprintf('%s', str_replace('NR-', '', $team->getName())),
                    'value' => 'Aucun tour',
                );
                continue;
            }
            $data[] = array(
                'label' => sprintf('%s (%s)', $timing->getRacer()->getNickname(), str_replace('NR-', '', $timing->getRacer()->getTeam()->getName())),
                'value' => $timing->getTiming()->format('H:i:s'),
            );
        }

        return $this->render('statistics/bestlaps.html.twig', array(
            'data' => $data
        ));
    }

}
