<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 *
 * @Route("/prediction")
 */
class PredictionController extends Controller
{

    /**
     *
     * @Route("/{id}", name="prediction_index")
     * @Template("AppBundle:Prediction:index.html.twig")
     * @Method("GET")
     *
     */
    public function indexAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $repoTeam = $em->getRepository('AppBundle:Team');
        $repoTiming = $em->getRepository('AppBundle:Timing');
        $repoRacer = $em->getRepository('AppBundle:Racer');
        $repoPause = $em->getRepository('AppBundle:RacerPause');

        $team = $repoTeam->find($id);

        try {
            $latestRacer = $repoTiming->getLatestRacer($team->getId());
        } catch(NoResultException $e) {
            return array(
                'team' => array(),
                'racersEndOfRotation' => array(),
                'nextRacers' => array(),
            );
        }

        $racers = $repoRacer->getAllByTeam($team);

        $pausesData = $repoPause->getTeamPauses($team);
        //var_dump($pausesData);
        /**
         * pauses ordering
         */
        //var_dump($pausesData[0]);
        $pauses = array();
        $pausesInfos = array();
        foreach($pausesData as $pauseData) {
            $p = $pauseData->getIdPause();
            $hour = $p->getHourStart()->format('YmdHi');
            $pauses[$hour][] = $pauseData->getIdRacer();
            $pausesInfos[$hour] = $pauseData;
        }
        foreach($pausesInfos as $k => $v) {
            $p = $v->getIdPause();
            if($p->getHourStop()->format('Hi') < $p->getHourStart()->format('Hi')) {
                $p->getHourStop()->modify('+1 day');
                var_dump($p->getHourStop()->format('r'));
            }
        }
        //var_dump($pauses);

        /**
         * end ordering
         */


        $racersEndOfRotation = $repoRacer->getNextRacersAvailable($team, $latestRacer->getPosition());

        $nextRacers = array();

        $racersEnd = array();
        $previousRacer = $latestRacer;
        // fixme, must start at latestRacer->latestTiming->getCreatedAt + timingAvg()
        $dt = new \Datetime();
        foreach($racersEndOfRotation as $racer) {
            $dt->modify(sprintf('+%d seconds', $previousRacer->getTimingAvg()));

            $d = clone $dt;
            $racersEnd[] = array(
                'racer' => $racer,
                'hour' => $d->format('H:i'),
            );
            $previousRacer = $racer;
        }
        //do {
        for($i = 0; $i < 50; $i++) {
            /*foreach($racers as $racer) {

                $dt->modify(sprintf('+%d seconds', $previousRacer->getTimingAvg()));
                $d = clone $dt;

                $nextRacers[] = array(
                    'racer' => $racer,
                    'hour'  => $d->format('H:i'),
                );
                $previousRacer = $racer;
            }*/
            //$hourPauses = array_keys($pauses);
            foreach($pauses as $hourPause => $racers)
            {
                $dtCheck = clone $dt;
                $dtCheck->modify(sprintf('+%d seconds', $previousRacer->getTimingAvg()));
                $hourStart = $dtCheck->format('YmdHi');
                $hourStop = $pausesInfos[$hourPause]->getIdPause()->getHourStop()->format('YmdHi');

                //$hourStopFixed = ($hourStop < $hourPause) ? $hourStop + 10000 : $hourStop;
                /*echo sprintf('checking if %d > %d AND %d < %d <br />',
                    $hourStart,
                    $hourPause,
                    $hourStart,
                    $hourStop
                ).PHP_EOL;*/
                if($hourStart > $hourPause && $hourStart < $hourStop) {
                    /*echo sprintf('stopping as %d > %d AND %d < %d <br />',
                        $hourStart,
                        $hourPause,
                        $hourStart,
                        $hourStop
                    ).PHP_EOL;*/
                    continue;
                }

                foreach($racers as $racer) {
                    $dt->modify(sprintf('+%d seconds', $previousRacer->getTimingAvg()));
                    $d = clone $dt;

                    $nextRacers[] = array(
                        'racer' => $racer,
                        'hour'  => $d->format('H:i'),
                    );
                    $previousRacer = $racer;
                }

            }
        }
        //} while($dontStop);

        return array(
            'team' => $team,
            'racersEndOfRotation' => $racersEnd,
            'nextRacers' => $nextRacers,
        );
    }
}
