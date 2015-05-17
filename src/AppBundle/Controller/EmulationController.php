<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

class EmulationController extends Controller
{

    /**
     * @Route("/compare/{id}", name="compare_team")
     * @Template()
     */
    public function compareAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $repoTiming = $em->getRepository('AppBundle:Timing');
        $repoMatsport = $em->getRepository('AppBundle:Matsport');
        $repoTeam = $em->getRepository('AppBundle:Team');

        try {
            $team = $repoTeam->find($id);
        } catch (\Exception $e) {
            $this->throwNotFoundException();
        }
        !$team && $this->throwNotFoundException();

        $teamTimings = $repoTiming->getTeamStats($team);
        $teamEmulations = $repoMatsport->getTeamStats($team);

        $minNb = max(count($teamTimings), count($teamEmulations));

        $data = array();
        for($i = 0; $i < $minNb; $i++)
        {
            $data[] = array(
                'timing' => isset($teamTimings[$i]) ? $teamTimings[$i] : null,
                'emulation' => isset($teamEmulations[$i]) ? $teamEmulations[$i] : null,
            );
        }

        return array(
            'data' => $data,
        );

    }

    /**
     * @Route("/compare", name="compare_list")
     * @Template()
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repoTeam = $em->getRepository('AppBundle:Team');
        $teams = $repoTeam->findAll();

        return array('teams' => $teams);
    }

    /**
     * @Route("/file", name="compare_file")
     */
    public function fileAction()
    {
        $file = $this->get('service_container')->getParameter('kernel.root_dir').'/matsport.html';

        return new Response(file_get_contents($file));
    }
}
