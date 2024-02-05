<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\TimingFixType;

use App\Entity\Timing;

use App\Repository\TeamRepository;
use App\Repository\TimingRepository;

use App\Service\TimingSeparator;

/**
 * Fix timings.
 *
 * @Route("/timing-fix")
 */
#[Route("/timing-fix")]
class TimingFixController extends AbstractController
{

    /**
     *
     * @Route("/", name="timing_fix_index")
     * Method("GET")
     * @Template("AppBundle:TimingFix:teams.html.twig")
     */
    #[Route("/", name: "timing_fix_index", methods: ['GET'])]
    public function indexTeamsAction(
        TeamRepository $teamRepository,
    )
    {
        $teams = $teamRepository->getAllWithRacers();

        return $this->render('TimingFix/teams.html.twig', array(
            'teams' => $teams,
        ));
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
    #[Route("/{id}", name: "timing_fix_team", methods: ['GET'])]
    public function listAction(
        $id,
        TimingRepository $timingRepository,
    )
    {
        $entities = $timingRepository->getLatestRacers($id);

        return $this->render('TimingFix/list.html.twig', array(
            'entities' => $entities,
        ));
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
    #[Route("/timing-fix/edit/{id}", name: "timing_fix_edit", methods: ['GET'])]
    public function editAction(
        $id,
        TimingRepository $timingRepository,
    )
    {
        //$entity = $em->getRepository('AppBundle:Timing')->find($id);
        $timing = $timingRepository->find($id);

        if (!$timing)
        {
            throw $this->createNotFoundException('Unable to find Timing entity.');
        }

        $editForm = $this->createEditForm($timing);
        //$deleteForm = $this->createDeleteForm($id);

        $newTiming = clone $timing; //new Timing();
        $newTiming->setTiming(new \Datetime('00:00:00'));
        //$newTiming->setId(0);

        return $this->render("TimingFix/edit.html.twig", array(
            'entity'      => $timing,
            'form'   => $editForm,
            'newEntity'   => $newTiming,
            //'delete_form' => $deleteForm->createView(),
        ));
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
        $form = $this->createForm(TimingFixType::class, $entity, array(
            'action' => $this->generateUrl('timing_fix_update', array('id' => $entity->getId())),
            //'method' => 'PUT',
        ));

        //$form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Timing entity.
     *
     * @Route("/{id}", name="timing_fix_update")
     * @Method("PUT")
     * @Template("AppBundle:TimingFix:edit.html.twig")
     */
    #[Route("/{id}", name: "timing_fix_update", methods: ['POST'])]
    public function updateAction(
        $id,
        EntityManagerInterface $em,
        TimingRepository $timingRepository,
        TimingSeparator $timingSeparator,
    )
    {
        $request = Request::createFromGlobals();

        $entity = $timingRepository->find($id);

        if (!$entity)
        {
            throw $this->createNotFoundException('Unable to find Timing entity.');
        }

        $timingModifications = clone $entity;

        //$deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($timingModifications);
        $editForm->handleRequest($request);

        $newTiming = clone $entity;


        if ($editForm->isSubmitted() && $editForm->isValid())
        {
            //$timingSeparator = $this->get('timing.separator');
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

        return $this->render('TimingFix/edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm,
            'newEntity' => $newTiming,
            //'delete_form' => $deleteForm->createView(),
        ));
    }
}
