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
     * @Template("AppBundle:Stats:team.html.twig")
     */
    public function teamAction($id) {
        $em = $this->getDoctrine()->getManager();
        $repoTiming = $em->getRepository('AppBundle:Timing');
        $repoTeam = $em->getRepository('AppBundle:Team');
        $team = $repoTeam->find($id);

        $timings = $repoTiming->getTeamStats($team);

        $t = array();

        foreach($timings as $timing) {
            $racerId = $timing->getIdRacer()->getId();
            $t[$racerId][] = $timing;
        }

        return array(
            'team' => $team,
            'timings' => $t,
        );

    }
}
