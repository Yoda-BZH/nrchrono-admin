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
        
        $racersEndOfRotation = $repoRacer->getNextRacersAvailable($team, $latestRacer->getPosition());
        
        $nextRacers = array();
        
        $racersEnd = array();
        $previousRacer = $latestRacer;
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
        for($i = 0; $i < 10; $i++) {
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
        //} while($dontStop);
            
        return array(
            'team' => $team,
            'racersEndOfRotation' => $racersEnd,
            'nextRacers' => $nextRacers,
        );
    }
}
