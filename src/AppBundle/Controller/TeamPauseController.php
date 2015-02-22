<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Visualisation des pauses par team
 *
 * @Route("/teampause")
 */
class TeamPauseController extends Controller
{
    /**
     * @Route("/view/{id}", name="team_pauses")
     * @Method("GET")
     * @Template()
     */
    public function indexAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $teamPauses = $em->getRepository('AppBundle:RacerPause')->getTeamPauses($id);

        return array(
            'teamPauses' => $teamPauses,
        );
    }
    /**
     * Creates a new Racer entity.
     *
     * @Route("/", name="teampause_order")
     * @Method("POST")
     */
    public function orderAction(Request $request)
    {

        try {
            $order = array_filter($request->request->get('order'));

            $em = $this->getDoctrine()->getManager();
            $repoRacerPause = $em->getRepository('AppBundle:RacerPause');

            foreach($order as $teamPauseId => $newPosition) {
                $teamPause = $repoRacerPause->find($teamPauseId);
                $teamPause
                    ->setPorder($newPosition)
                    ;
                $em->persist($teamPause);
            }
            $em->flush();

            //$entity = $em->getRepository('AppBundle:Racer')->find($id);
            $response = json_encode(array('data' => 'Les modifications ont été enregistrées.'));
            $httpCode = Response::HTTP_OK;

        } catch (\Exception $e) {
            $response = json_encode(array(
                'data' => 'Impossible de sauvegarder les modifications : ' . PHP_EOL . $e->getMessage()
            ));
            $httpCode = Response::HTTP_BAD_REQUEST;
        }

        return new Response(
            $response,
            $httpCode,
            array('Content-Type' => 'application/json')
        );
    }

}
