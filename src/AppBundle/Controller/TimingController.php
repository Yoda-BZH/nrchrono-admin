<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Timing;
use AppBundle\Form\TimingType;

/**
 * Timing controller.
 *
 * @Route("/timing")
 */
class TimingController extends Controller
{

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
        $repoRacer = $em->getRepository('AppBundle:Racer');
        $repoTiming = $em->getRepository('AppBundle:Timing');
        $teams = $repoTeam->findAll();
$teams = array($teams[0]);
        $latestTimings = array();
        foreach($teams as $team) {
            $nextGuesser = $this->get('racer.next');
            $nextRacer = $nextGuesser
                ->setTeam($team)
                ->getNext()
                ;

            if(!$nextRacer) {
                $repoRacer = $em->getRepository('AppBundle:Racer');
                $nextRacer = $repoRacer->getFirstOfTeam($team);
            }

            $latestRacer = $nextGuesser->getLatest();

            try {
                $latestTeamTiming = $repoTiming->getLatestTeamTiming($team);
                $clock = clone $latestTeamTiming->getClock();
            } catch(\Exception $e) {
                $race = $em->getRepository('AppBundle:Race')->find(1);
                $clock = clone $race->getStart();
            }

            if(!$nextRacer) {
                $nextRacer = $repoRacer->getFirstOfTeam($team);
                //return new JsonResponse(array(), 404);
            }
            $arrival = clone $clock;
            $interval = new \DateInterval($latestRacer->getTimingAvg()->format('\P\TH\Hi\Ms\S'));
            $arrival->add($interval);

            $delta = $arrival->diff(new \Datetime());
            $latestTimings[$team->getId()] = array(
                'racer' => $nextRacer,
                'latest' => $latestRacer,
                'clock' => $clock,
                'team'  => $team,
                'arrival' => $arrival,
                'delta' => $delta,
            );
        }

        return array(
            'latestTimings' => $latestTimings,
        );
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

        $entity = $em->getRepository('AppBundle:Timing')->find($id);

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

        if(!$nextRacer) {
            $repoRacer = $em->getRepository('AppBundle:Racer');
            $nextRacer = $repoRacer->getFirstOfTeam($team);
            //return new JsonResponse(array(), 404);
        }

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
}
