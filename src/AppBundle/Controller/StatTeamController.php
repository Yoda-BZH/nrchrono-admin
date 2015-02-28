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
        
    }
}
