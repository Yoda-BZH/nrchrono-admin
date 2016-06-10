<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Entity\Timing;
use AppBundle\Form\TimingFixType;


/**
 * Fix timings.
 *
 * @Route("/timing-fix")
 */
class TimingFixController extends Controller
{

    /**
     *
     * @Route("/", name="timing_fix_index")
     * Method("GET")
     * @Template("AppBundle:TimingFix:teams.html.twig")
     */
    public function indexTeamsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repoTeam = $em->getRepository('AppBundle:Team');
        $teams = $repoTeam->getAllWithRacers();

        return array(
            'teams' => $teams,
        );
    }


    /**
     * description
     *
     * @return void
     *
     * @Method("GET")
     * @Route("/{id}", name="timing_fix_team")
     * @Template("AppBundle:TimingFix:list.html.twig")
     */
    public function listAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository('AppBundle:Timing');

        $entities = $repo->getLatestRacers($id);

        return array(
            'entities' => $entities,
        );
    }

    /**
     * description
     *
     * @return void
     *
     * @Method("GET")
     * @Route("/timing-fix/edit/{id}", name="timing_fix_edit")
     * @Template("AppBundle:TimingFix:edit.html.twig")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Timing')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Timing entity.');
        }

        $editForm = $this->createEditForm($entity);
        //$deleteForm = $this->createDeleteForm($id);

        $newTiming = clone $entity; //new Timing();
        $newTiming->setTiming(new \Datetime('00:00:00'));
        //$newTiming->setId(0);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'newEntity'   => $newTiming,
            //'delete_form' => $deleteForm->createView(),
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
        $form = $this->createForm(new TimingFixType(), $entity, array(
            'action' => $this->generateUrl('timing_fix_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Timing entity.
     *
     * @Route("/{id}", name="timing_fix_update")
     * @Method("PUT")
     * @Template("AppBundle:TimingFix:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Timing')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Timing entity.');
        }
        
        $timingModifications = clone $entity;

        //$deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($timingModifications);
        $editForm->handleRequest($request);
        
        $newTiming = clone $entity;


        if ($editForm->isValid()) {
            $timingSeparator = $this->get('timing.separator');
            $timingSeparator
                ->setOriginal($entity)
                ->setNew($newTiming)
                ->setDatas($timingModifications)
                ->compute()
                ;
            
            $em->persist($entity);
            $em->persist($newTiming);
            $em->flush();

            return $this->redirect($this->generateUrl('timing_edit', array('id' => $newTiming->getId())));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            //'delete_form' => $deleteForm->createView(),
        );
    }
}
