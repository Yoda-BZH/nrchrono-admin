<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/stats/racer")
 */
class StatRacerController extends Controller
{
    /**
     * @Route("/", name="stats_racers")
     */
    public function racersAction() {

    }

    /**
     * @Route("/{id}", name="stats_racer")
     * @Template("AppBundle:Stats:racer.html.twig")
     */
    public function racerAction($id) {
        $em = $this->getDoctrine()->getManager();
        $repoTiming = $em->getRepository('AppBundle:Timing');
        $repoRacer = $em->getRepository('AppBundle:Racer');
        $racer = $repoRacer->find($id);
        if(!$racer) {
            throw $this->createNotFoundException(sprintf('Racer %d not found', $id));
        }

        $timings = $repoTiming->getRacerStats($racer);

        return array(
            'racer' => $racer,
            'timings' => $timings,
        );
    }
}
