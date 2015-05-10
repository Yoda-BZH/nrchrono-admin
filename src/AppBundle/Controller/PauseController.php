<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Pause;
use AppBundle\Form\PauseType;

/**
 * Pause controller.
 *
 * @Route("/pause")
 */
class PauseController extends Controller
{

    /**
     * Lists all Pause entities.
     *
     * @Route("/", name="pause")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppBundle:Pause')->findAllWithTeam();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Pause entity.
     *
     * @Route("/", name="pause_create")
     * @Method("POST")
     * @Template("AppBundle:Pause:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Pause();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('pause_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Pause entity.
     *
     * @param Pause $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Pause $entity)
    {
        $form = $this->createForm(new PauseType(), $entity, array(
            'action' => $this->generateUrl('pause_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Pause entity.
     *
     * @Route("/new", name="pause_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Pause();
        $entity->setHourStart(new \Datetime('today 00:00'));
        $entity->setHourStop(new \Datetime('today 00:00'));
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Pause entity.
     *
     * @Route("/{id}", name="pause_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Pause')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Pause entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Pause entity.
     *
     * @Route("/{id}/edit", name="pause_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Pause')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Pause entity.');
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
    * Creates a form to edit a Pause entity.
    *
    * @param Pause $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Pause $entity)
    {
        $form = $this->createForm(new PauseType(), $entity, array(
            'action' => $this->generateUrl('pause_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Pause entity.
     *
     * @Route("/{id}", name="pause_update")
     * @Method("PUT")
     * @Template("AppBundle:Pause:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Pause')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Pause entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('pause_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Pause entity.
     *
     * @Route("/{id}", name="pause_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:Pause')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Pause entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('pause'));
    }

    /**
     * Creates a form to delete a Pause entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('pause_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
