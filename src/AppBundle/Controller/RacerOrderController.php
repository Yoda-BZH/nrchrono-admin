<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Racer;
use AppBundle\Form\RacerType;

/**
 * Racer controller.
 *
 * @Route("/racerorder")
 */
class RacerOrderController extends Controller
{
    /**
     * Creates a new Racer entity.
     *
     * @Route("/", name="racer_order")
     * @Method("POST")
     */
    public function orderAction(Request $request)
    {

        try {
            $order = array_filter($request->request->get('order'));

            $em = $this->getDoctrine()->getManager();
            $repoRacer = $em->getRepository('AppBundle:Racer');

            foreach($order as $racerId => $newPosition) {
                $racer = $repoRacer->find($racerId);
                $racer
                    ->setPosition($newPosition)
                    ;
                $em->persist($racer);
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
