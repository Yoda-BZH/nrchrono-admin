<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\RacerPause;
use AppBundle\Form\RacerPauseType;

/**
 * RacerPause controller.
 *
 * @Route("/racerpause")
 */
class RacerPauseController extends Controller
{

    /**
     * Lists all RacerPause entities.
     *
     * @Route("/", name="racerpause")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppBundle:RacerPause')->findAllWithRacerTeamPause();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new RacerPause entity.
     *
     * @Route("/", name="racerpause_create")
     * @Method("POST")
     * @Template("AppBundle:RacerPause:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new RacerPause();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('racerpause_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a RacerPause entity.
     *
     * @param RacerPause $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(RacerPause $entity)
    {
        $form = $this->createForm(new RacerPauseType(), $entity, array(
            'action' => $this->generateUrl('racerpause_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new RacerPause entity.
     *
     * @Route("/new", name="racerpause_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new RacerPause();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a RacerPause entity.
     *
     * @Route("/{id}", name="racerpause_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:RacerPause')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find RacerPause entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing RacerPause entity.
     *
     * @Route("/{id}/edit", name="racerpause_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:RacerPause')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find RacerPause entity.');
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
    * Creates a form to edit a RacerPause entity.
    *
    * @param RacerPause $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(RacerPause $entity)
    {
        $form = $this->createForm(new RacerPauseType(), $entity, array(
            'action' => $this->generateUrl('racerpause_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing RacerPause entity.
     *
     * @Route("/{id}", name="racerpause_update")
     * @Method("PUT")
     * @Template("AppBundle:RacerPause:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:RacerPause')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find RacerPause entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('racerpause_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a RacerPause entity.
     *
     * @Route("/{id}", name="racerpause_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:RacerPause')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find RacerPause entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('racerpause'));
    }

    /**
     * Creates a form to delete a RacerPause entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('racerpause_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
