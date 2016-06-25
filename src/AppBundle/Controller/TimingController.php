<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Timing;
use AppBundle\Form\TimingType;
use Doctrine\ORM\NoResultException;

/**
 * Timing controller.
 *
 * @Route("/timing")
 */
class TimingController extends Controller
{

    /**
     * description
     *
     * @param void
     * @return void
     */
    private function logger()
    {
        return $this->get('logger');
    }

    /**
     * Lists all Timing entities.
     *
     * @Route("/", name="timing")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppBundle:Timing')->findAllWithRacerTeam();

        $teams = $em->getRepository('AppBundle:Team')->findAll();

        return array(
            'teams' => $teams,
            'entities' => $entities,
        );
    }


    /**
     *
     * @Route("/status", name="timing_status")
     * Method("GET")
     * @Template("AppBundle:Timing:status.html.twig")
     */
    public function statusAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repoTeam = $em->getRepository('AppBundle:Team');
        $teams = $repoTeam->getAllWithRacers();

        return array(
            'teams' => $teams,
        );
    }

    /**
     *
     * @Route("/status/{id}", name="timing_status_team")
     * Method("GET")
     * @Template("AppBundle:Timing:statusTeam.html.twig")
     */
    public function statusTeamAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $repoTeam = $em->getRepository('AppBundle:Team');
        $repoRacer = $em->getRepository('AppBundle:Racer');
        $repoTiming = $em->getRepository('AppBundle:Timing');
        $team = $repoTeam->getWithRacersByPosition($id);
        $latestTimings = array();
        $now = new \Datetime();

        // previous timings
        $previousTimings = array(null, null, null);
        $foundPreviousTimings = $repoTiming->getLatestTeamTiming($team, 3);
        foreach($previousTimings as $index => $value) {
            if(isset($foundPreviousTimings[$index])) {
                $previousTimings[$index] = $foundPreviousTimings[$index];
            }
        }
        $previousTimings = array_reverse($previousTimings);
        // end previous timings

        $nextGuesser = $this->get('racer.next');
        $nextRacers = $nextGuesser
            ->setTeam($team)
            ->getNexts()
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
            $race = $this->get('race')->get(); //$em->getRepository('AppBundle:Race')->find(1);
            $clock = clone $race->getStart();
        }

        //if(!$latestRacer) {
        //    $latestRacer = $repoRacer->getFirstOfTeam($team);
        //    //return new JsonResponse(array(), 404);
        //}
        $arrival = clone $clock;
        $interval = new \DateInterval($nextRacers[0]->getTimingAvg()->format('\P\TH\Hi\Ms\S'));
        $arrival->add($interval);

        $delta = $arrival->diff($now);
        return array(
            'racers' => array_slice($nextRacers, 0, 5),
            //'latest' => $latestRacer,
            'clock' => $clock,
            'team'  => $team,
            'arrival' => $arrival,
            'delta' => $delta,
            'previous' => $previousTimings,
            'predictions' => $nextGuesser->getPredictions($id)
        );
    }

    /**
     * Alter a racer for an existing Timing
     *
     * @Route("/update-timing", name="timing_update_data")
     * @Method("POST")
     */
    public function alterAction(Request $request)
    {
        $data = $request->request->get('data');
        $em = $this->getDoctrine()->getManager();

        $repoTiming = $em->getRepository('AppBundle:Timing');
        $timingId = $data[0]['timing'];

        $timing = $repoTiming->find($timingId);
        //if ($timing->isPrediction())
        //{
        //    throw new \Exception('Cannot modify a prediction');
        //}

        $repoRacer = $em->getRepository('AppBundle:Racer');
        $racerId = $data[0]['racerid'];
        // FIXME, Check if racer is in the correct team !
        $racer = $repoRacer->find($racerId);
        $timing->setIdRacer($racer);

        $this->logger()->info(sprintf('Changing racer for timing %d to %s', $timing->getId(), $racer->getNickname()));

        $em->persist($timing);
        $em->flush();

        return new Response();
    }

    /**
     * Creates a new Timing entity.
     *
     * @Route("/", name="timing_create")
     * @Method("POST")
     * @Template("AppBundle:Timing:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Timing();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('timing_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Timing entity.
     *
     * @param Timing $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Timing $entity)
    {
        $form = $this->createForm(new TimingType(), $entity, array(
            'action' => $this->generateUrl('timing_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Timing entity.
     *
     * @Route("/new", name="timing_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Timing();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Timing entity.
     *
     * @Route("/{id}", name="timing_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Timing')->findLatests($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Timing entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Timing entity.
     *
     * @Route("/{id}/edit", name="timing_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Timing')->findLatests($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Timing entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Timing entity.
    *
    * @param Timing $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Timing $entity)
    {
        $form = $this->createForm(new TimingType(), $entity, array(
            'action' => $this->generateUrl('timing_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Timing entity.
     *
     * @Route("/{id}", name="timing_update")
     * @Method("PUT")
     * @Template("AppBundle:Timing:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Timing')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Timing entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('timing_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Timing entity.
     *
     * @Route("/{id}", name="timing_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:Timing')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Timing entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('timing'));
    }

    /**
     * get next racer
     *
     * @Route("/next/{id}", name="timing_next")
     * @Method("GET")
     */
     public function getNextAction($id) {

        $em = $this->getDoctrine()->getManager();
        $repoTeam = $em->getRepository('AppBundle:Team');
        $team = $repoTeam->find($id);

        $nextGuesser = $this->get('racer.next');
        $nextRacer = $nextGuesser
            ->setTeam($team)
            ->getNext()
            ;

        //if(!$nextRacer) {
        //    $repoRacer = $em->getRepository('AppBundle:Racer');
        //    $nextRacer = $repoRacer->getFirstOfTeam($team);
        //    //return new JsonResponse(array(), 404);
        //}

        $d = array(
            'firstname' => $nextRacer->getFirstname(),
            'lastname' => $nextRacer->getLastname(),
            'nickname' => $nextRacer->getNickname(),
            'position' => $nextRacer->getPosition(),
            'team' => array(
                'name' => $team->getName(),
            ),
        );

        return new JsonResponse($d);
     }

    /**
     * Creates a form to delete a Timing entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('timing_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

    /**
     * Ajoute une entrée de passage manuellement
     *
     * @Route("/timing/manual", name="timing_add_manual")
     * @Method("POST")
     */
    public function manualAction(Request $request)
    {
        $data = $request->request->get('data');
        $em = $this->getDoctrine()->getManager();

        $repoRacer = $em->getRepository('AppBundle:Racer');
        $racer = $repoRacer->find($data['racerid']);

        $repoTeam = $em->getRepository('AppBundle:Team');
        $team = $repoTeam->find($data['teamid']);

        try {
            $repoTiming = $em->getRepository('AppBundle:Timing');
            $latestTeamTiming = $repoTiming->getLatestTeamTiming($team, 1);
            $previousClock = clone $latestTeamTiming->getClock();
        // FIXME no result exception
        } catch(NoResultException $e) {
            $repoRace = $em->getRepository('AppBundle:Race');
            $race = $this->get('race')->get();
            $previousClock = clone $race->getStart();
        }

        $timingId = $data['timing'];
        $em = $this->getDoctrine()->getManager();

        $repoTiming = $em->getRepository('AppBundle:Timing');
        $timing = $repoTiming->find($timingId);
        $now = new \DateTime();

        $intervalT = $previousClock->diff($now);
        $t = new \Datetime('today '.$intervalT->format('%H:%I:%S'));

        //$timing = new Timing();
        $timing
            ->setCreatedAt($now)
            ->setIdRacer($racer)
            ->setIsRelay(0)
            ->setClock($now)
            ->setManual()
            ->setTiming($t)
            ;
        $em->persist($timing);
        $em->flush();

        return new Response();
    }

    /**
     * Annule un départ
     *
     * @Route("/timing/revert-departure", name="timing_revert_departure")
     * @Method("POST")
     */
    public function revertDeparture(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $timingId = $request->request->get('timing');
        $repoTiming = $em->getRepository('AppBundle:Timing');

        $timing = $repoTiming->find($timingId);
        $timing
            ->setPrediction()
            ->setTiming(null)
            ;
        $em->persist($timing);
        $em->flush();
        $this->logger()->info(sprintf('reset timing id %d to prediction for racer "%s"', $timing->getId(), $timing->getIdRacer()->getNickname()));

        return new Response();
    }
}
