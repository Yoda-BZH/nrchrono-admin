<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Ranking;
use App\Form\RankingType;
use App\Repository\RankingRepository;

/**
 * Ranking controller.
 *
 * @Route("/ranking")
 */
#[Route("/ranking")]
class RankingController extends AbstractController
{

    /**
     * Lists all Ranking entities.
     *
     * @Route("/", name="ranking")
     * @Method("GET")
     * @Template()
     */
    #[Route("/", name: "ranking", methods: ['GET'])]
    public function indexAction(
        RankingRepository $rankingRepository
    )
    {
        $entities = $rankingRepository->findAll();

        return $this->render('Ranking/index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Ranking entity.
     *
     * @Route("/", name="ranking_create")
     * @Method("POST")
     * @Template("AppBundle:Ranking:new.html.twig")
     */
    #[Route("/", name: "ranking_create", methods: ["POST"])]
    public function createAction(
        EntityManagerInterface $em,
    )
    {
        $request = Request::createFromGlobals();

        $entity = new Ranking();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('ranking_show', array('id' => $entity->getId())));
        }

        return $this->render('Ranking/new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Ranking entity.
     *
     * @param Ranking $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Ranking $entity)
    {
        $form = $this->createForm(RankingType::class, $entity, array(
            'action' => $this->generateUrl('ranking_create'),
            'method' => 'POST',
        ));

        //$form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Ranking entity.
     *
     * @Route("/new", name="ranking_new")
     * @Method("GET")
     * @Template()
     */
    #[Route("/new", name: "ranking_new", methods: ['GET'])]
    public function newAction()
    {
        $entity = new Ranking();
        $form   = $this->createCreateForm($entity);

        return $this->render('Ranking/new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Ranking entity.
     *
     * @Route("/{id}", name="ranking_show")
     * @Method("GET")
     * @Template()
     */
    #[Route("/{id}", name: "ranking_show", methods: ['GET'])]
    public function showAction(
        $id,
        RankingRepository $rankingRepository,
    )
    {
        $entity = $rankingRepository->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Ranking entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('Ranking/show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Ranking entity.
     *
     * @Route("/{id}/edit", name="ranking_edit")
     * @Method("GET")
     * @Template()
     */
    #[Route("/{id}/edit", name: "ranking_edit", methods: ['GET'])]
    public function editAction(
        $id,
        RankingRepository $rankingRepository,
    )
    {
        $entity = $rankingRepository->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Ranking entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('Ranking/edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Ranking entity.
    *
    * @param Ranking $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Ranking $entity)
    {
        $form = $this->createForm(new RankingType(), $entity, array(
            'action' => $this->generateUrl('ranking_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        //$form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Ranking entity.
     *
     * @Route("/{id}", name="ranking_update")
     * @Method("PUT")
     * @Template("AppBundle:Ranking:edit.html.twig")
     */
    #[Route("/{id}", name: "ranking_update", methods: ['PUT'])]
    public function updateAction(
        $id,
        RankingRepository $rankingRepository,
        EntityManagerInterface $em,
    )
    {
        $request = Request::createFromGlobals();

        $entity = $rankingRepository->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Ranking entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('ranking_edit', array('id' => $id)));
        }

        return $this->render('Ranking/edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Ranking entity.
     *
     * @Route("/{id}", name="ranking_delete")
     * @Method("DELETE")
     */
    #[Route("/{id}", name: "ranking_delete", methods: ['DELETE'])]
    public function deleteAction(
        $id,
        RankingRepository $rankingRepository,
        EntityManagerInterface $em,
    )
    {
        $request = Request::createFromGlobals();
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity = $rankingRepository->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Ranking entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('ranking'));
    }

    /**
     * Creates a form to delete a Ranking entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('ranking_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
