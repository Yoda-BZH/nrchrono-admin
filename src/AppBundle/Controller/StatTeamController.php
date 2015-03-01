<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/stats/team")
 */
class StatTeamController extends Controller
{
    /**
     * @Route("/", name="stats_teams")
     */
    public function teamsAction() {

    }

    /**
     * @Route("/{id}", name="stats_team")
     */
    public function teamAction($id) {
        $em = $this->getDoctrine()->getManager();
        $repoTiming = $em->getRepository('AppBundle:Timing');
        $repoTeam = $em->getRepository('AppBundle:Team');
        $team = $repoRacer->find($id);
        
        //$timings = $repoTiming->getRacerStats($racer);

        return array(
            'racer' => $racer,
            'timings' => $timings,
        );
        
    }
}
