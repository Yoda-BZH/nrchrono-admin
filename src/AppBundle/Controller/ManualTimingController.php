<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;


/**
 * @Route("/manual-timing")
 */
class ManualTimingController extends Controller
{
    /**
     * @Route("/index", name="manual_timing")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repoTeam = $em->getRepository('AppBundle:Team');
        $teams = $repoTeam->getAll();

        return array(
            'teams' => $teams,
        );
    }

    /**
     * @Route("/add")
     * @Template()
     */
    public function addAction()
    {
        return array(
            // ...
        );
    }

    /**
     * @Route("/status/{id}", name="manual_status_team")
     */
    public function statusAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $repoTeam = $em->getRepository('AppBundle:Team');
        $repoRacer = $em->getRepository('AppBundle:Racer');
        //$repoTiming = $em->getRepository('AppBundle:Timing');
        $team = $repoTeam->find($id);
        $latestTimings = array();
        $now = new \Datetime();

        $nextGuesser = $this->get('racer.next');
        $nextRacer = $nextGuesser
            ->setTeam($team)
            ->getNext()
            ;

        //if(!$nextRacer) {
        //    $repoRacer = $em->getRepository('AppBundle:Racer');
        //    $nextRacer = $repoRacer->getFirstOfTeam($team);
        //}

        $latestRacer = $nextGuesser->getLatest();

        try {
            //$latestTeamTiming = $repoTiming->getLatestTeamTiming($team);
            $latestTeamTiming = $nextGuesser->getLatestTiming();
            if(!$latestTeamTiming) {
                throw new \Exception();
            }
            $clock = clone $latestTeamTiming->getClock();
        } catch(\Exception $e) {
            $race = $em->getRepository('AppBundle:Race')->find(1);
            $clock = clone $race->getStart();
        }

        //if(!$latestRacer) {
        //    $latestRacer = $repoRacer->getFirstOfTeam($team);
        //    //return new JsonResponse(array(), 404);
        //}
        $arrival = clone $clock;
        $interval = new \DateInterval($nextRacer->getTimingAvg()->format('\P\TH\Hi\Ms\S'));
        $arrival->add($interval);

        //$delta = $arrival->diff($now);
        $timingData = array(
            'racer' => $nextRacer,
            //'latest' => $latestRacer,
            'clock' => $clock,
            'team'  => $team,
            'arrival' => $arrival,
            //'delta' => $delta,
        );

        return $timingData;
    }

}
