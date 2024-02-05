<?php

namespace App\Controller;

#use App\Entity\Race;
use App\Entity\Timing;
use App\Form\TimingType;
#use App\Form\Race1Type;
use App\Repository\TimingRepository;
use App\Repository\TeamRepository;
use App\Repository\RacerRepository;
use App\Repository\RaceRepository;
#use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

use Doctrine\ORM\NoResultException;
use Doctrine\ORM\EntityManagerInterface;

use App\Service\NextRacerGuesser;
use App\Service\RaceManager;

use Psr\Log\LoggerInterface;

/**
 * Timing controller.
 *
 * @Route("/timing")
 */
#[Route("/timing")]
class TimingController extends AbstractController
{

    /**
     * Lists all Timing entities.
     *
     * @Route("/", name="timing")
     * @Method("GET")
     * @Template()
     */
    #[Route("/", name: "timing")]
    public function indexAction(TimingRepository $timingRepository, TeamRepository $teamRepository)
    {
        $entities = $timingRepository->findAllWithRacerTeam();

        return $this->render('Timing/index.html.twig', array(
            'entities' => $entities,
        ));
    }


    /**
     *
     * @Route("/status", name="timing_status")
     * Method("GET")
     * @Template("AppBundle:Timing:status.html.twig")
     */
    #[Route("/status", name: "timing_status", methods: ["GET"])]
    public function statusAction(
        TeamRepository $teamRepository,
        RaceRepository $raceRepository,
        Request $request,
    ): Response
    {
        $race = $raceRepository->getCurrentRace();

        $session = $request->getSession();
        $team_filter = $session->get('team_filter', 0);
        $teams = $teamRepository->getAllWithRacers($team_filter);

        if (!$race)
        {
            return $this->redirectToRoute('race_index');
        }

        $session = $request->getSession();
        $racers_after = $session->get('pref_status_list_team_after', 3);
        $racers_before = $session->get('pref_status_list_team_before', 5);

        return $this->render('Timing/status.html.twig', array(
            'race' => $race,
            'teams' => $teams,
            'nb_after' => $racers_after,
            'nb_before' => $racers_before,
        ));
    }

    /**
     *
     * @Route("/status/{id}", name="timing_status_team")
     * Method("GET")
     * @Template("AppBundle:Timing:statusTeam.html.twig")
     */
    #[Route("/status/{id}", name: "timing_status_team", methods: ['GET'])]
    public function statusTeamAction(
        $id,
        TeamRepository $teamRepository,
        RacerRepository $racerRepository,
        TimingRepository $timingRepository,
        NextRacerGuesser $nextGuesser,
        RaceManager $raceManager,
        Request $request,
    )
    {
        $session = $request->getSession();
        $team = $teamRepository->getWithRacersByPosition($id);
        $latestTimings = array();
        $now = new \Datetime();

        // previous timings
        $nbPrevious = $session->get('pref_status_list_team_after', 3);
        $previousTimings = array_fill(0, $nbPrevious, null);
        $foundPreviousTimings = $timingRepository->getLatestTeamTiming($team, $nbPrevious);
        if ($nbPrevious == 1)
        {
            $foundPreviousTimings = array($foundPreviousTimings);
        }

        foreach($previousTimings as $index => $value)
        {
            if(isset($foundPreviousTimings[$index]))
            {
                $previousTimings[$index] = $foundPreviousTimings[$index];
            }
        }
        $previousTimings = array_reverse($previousTimings);
        // end previous timings

        $nbNext = $session->get('pref_status_list_team_before', 5);
        //$nextGuesser = $this->get('racer.next');
        $nextRacers = $nextGuesser
            ->setTeam($team)
            ->getNexts($nbNext)
            ;

        //if(!$nextRacer) {
        //    $racerRepository = $em->getRepository('AppBundle:Racer');
        //    $nextRacer = $racerRepository->getFirstOfTeam($team);
        //}

        $latestRacer = $nextGuesser->getLatest();

        try
        {
            //$latestTeamTiming = $timingRepositor->getLatestTeamTiming($team);
            $latestTeamTiming = $nextGuesser->getLatestTiming();
            if(!$latestTeamTiming) {
                throw new \Exception();
            }
            $clock = clone $latestTeamTiming->getClock();
        }
        catch(\Exception $e)
        {
            $race = $raceManager->get(); //$em->getRepository('AppBundle:Race')->find(1);
            $clock = clone $race->getStart();
        }

        //if(!$latestRacer) {
        //    $latestRacer = $racerRepository->getFirstOfTeam($team);
        //    //return new JsonResponse(array(), 404);
        //}
        $arrivals = [];
        for($i = 0; $i < $nbNext; $i++)
        {
            if ($i == 0)
            {
                $arrivals[$i] = clone $clock;
            }
            else
            {
                $arrivals[$i] = clone $arrivals[$i - 1];
            }
            $interval = new \DateInterval($nextRacers[$i]->getTimingAvg()->format('\P\TH\Hi\Ms\S'));
            // $this->logger()->error(sprintf('calculating interval for team %s racer %s to %s', $team->getName(), $nextRacers[0]->getFirstName(), $arrival->format('r')));
            $arrivals[$i]->add($interval);
        }

        $delta = $arrivals[0]->diff($now);
        return $this->render('Timing/statusTeam.html.twig', array(
            'racers' => $nextRacers,
            //'latest' => $latestRacer,
            'clock' => $clock,
            'team'  => $team,
            'arrivals' => $arrivals,
            'delta' => $delta,
            'previous' => $previousTimings,
            'predictions' => $nextGuesser->getPredictions($id),
            //'arrivalhis' => $arrival->format('H:i:s'),
            //'arrivalc' => $arrival->format('c'),
            //'arrivalr' => $arrivals[0]->format('r'),
        ));
    }

    /**
     * Alter a racer for an existing Timing
     *
     * @Route("/update-timing", name="timing_update_data")
     * @Method("POST")
     */
    #[Route("/update-timing", name: "timing_update_data", methods: ['POST'])]
    public function alterAction(
        Request $request,
        EntityManagerInterface $em,
        TimingRepository $timingRepository,
        RacerRepository $racerRepository,
        LoggerInterface $logger,
    )
    {
        $postData = $request->request->all();
        $data = $postData['data'];

        $timingId = $data[0]['timing'];

        $timing = $timingRepository->find($timingId);
        $oldRacer = $timing->getRacer();
        //if ($timing->isPrediction())
        //{
        //    throw new \Exception('Cannot modify a prediction');
        //}

        $racerId = $data[0]['racerid'];
        // FIXME, Check if racer is in the correct team !
        $racer = $racerRepository->find($racerId);
        if ($racer->isPaused())
        {
            $predictions = $timingRepository->getFutureOf($timing, $racer->getTeam());

            foreach($predictions as $k => $prediction)
            {
                if (0 == $k)
                {
                    $racerForNext = $prediction->getRacer();

                    $prediction->setRacer($racer);
                    $em->persist($prediction);
                }
                else
                {
                    $r = $prediction->getRacer();
                    $prediction->setRacer($racerForNext);
                    $em->persist($prediction);
                    $racerForNext = $r;
                }
            }
        }
        else
        {
            $timing->setRacer($racer);
            $em->persist($timing);
        }

        $logger->info(sprintf('Changing racer for timing %d from %s to %s', $timing->getId(), $oldRacer->getNickname(), $racer->getNickname()));

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
    #[Route("/", name: "timing_create", methods: ['POST'])]
    public function createAction(
        Request $request,
        EntityManagerInterface $em,
    )
    {
        $entity = new Timing();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
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
        $form = $this->createForm(TimingType::class, $entity, array(
            'action' => $this->generateUrl('timing_create'),
            'method' => 'POST',
        ));

        //$form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Timing entity.
     *
     * @Route("/new", name="timing_new")
     * @Method("GET")
     * @Template()
     */
    #[Route("/new", name: "timing_new", methods: ['GET'])]
    public function newAction()
    {
        $entity = new Timing();
        $form   = $this->createCreateForm($entity);

        return $this->render('Timing/new.html.twig', array(
            'form'   => $form,
        ));
    }

    /**
     * Finds and displays a Timing entity.
     *
     * @Route("/{id}", name="timing_show")
     * @Method("GET")
     * @Template()
     */
    #[Route("/{id}", name: "timing_show", methods: ['GET'])]
    public function showAction(
        $id,
        TimingRepository $timingRepository,
    )
    {
        $entity = $timingRepository->findLatests($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Timing entity.');
        }

        //$deleteForm = $this->createDeleteForm($id);

        return $this->render('Timing/show.html.twig', array(
            'entity'      => $entity,
            //'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Timing entity.
     *
     * @Route("/{id}/edit", name="timing_edit")
     * @Method("GET")
     * @Template()
     */
    #[Route("/{id}/edit", name: "timing_edit", methods: ['GET'])]
    public function editAction(
        $id,
        TimingRepository $timingRepository,
    )
    {
        $entity = $timingRepository->findLatests($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Timing entity.');
        }

        $editForm = $this->createEditForm($entity);
        //$deleteForm = $this->createDeleteForm($id);

        return $this->render('Timing/edit.html.twig', array(
            'timing'      => $entity,
            'form'   => $editForm,
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
        $form = $this->createForm(TimingType::class, $entity, array(
            'action' => $this->generateUrl('timing_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        //$form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Timing entity.
     *
     * @Route("/{id}", name="timing_update")
     * @Method("PUT")
     * @Template("AppBundle:Timing:edit.html.twig")
     */
    #[Route("/{id}", name: "timing_update", methods: ['PUT'])]
    public function updateAction(
        $id,
        TimingRepository $timingRepository,
        EntityManagerInterface $em,
        Request $request,
    )
    {
        $entity = $timingRepository->find($id);

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

        return $this->render('Timing/edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Timing entity.
     *
     * @Route("/{id}", name="timing_delete")
     * @Method("DELETE")
     */
    #[Route("/{id}", name: "timing_delete", methods: ['DELETE'])]
    public function deleteAction(
        $id,
        EntityManagerInterface $em,
        TimingRepository $timingRepository,
        Request $request,
    )
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity = $timingRepository->find($id);

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
    #[Route("/next/{id}", name: "timing_next", methods: ['GET'])]
    public function getNextAction(
        $id,
        TeamRepository $teamRepository,
        NextRacerGuesser $nextGuesser,
    )
    {
        $team = $teamRepository->find($id);

        //$nextGuesser = $this->get('racer.next');
        $nextRacer = $nextGuesser
            ->setTeam($team)
            ->getNext()
            ;

        //if(!$nextRacer) {
        //    $racerRepository = $em->getRepository('AppBundle:Racer');
        //    $nextRacer = $racerRepository->getFirstOfTeam($team);
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
    //private function createDeleteForm($id)
    //{
    //    return $this->createFormBuilder()
    //        ->setAction($this->generateUrl('timing_delete', array('id' => $id)))
    //        ->setMethod('DELETE')
    //        //->add('submit', 'submit', array('label' => 'Delete'))
    //        ->getForm()
    //    ;
    //}

    /**
     * Ajoute une entrée de passage manuellement
     *
     * @Route("/timing/manual", name="timing_add_manual")
     * @Method("POST")
     */
    #[Route("/timing/manual", name: "timing_add_manual", methods: ['POST'])]
    public function manualAction(
        RaceManager $raceManager,
        RacerRepository $racerRepository,
        RaceRepository $raceRepository,
        TeamRepository $teamRepository,
        TimingRepository $timingRepository,
        EntityManagerInterface $em,
        Request $request,
    )
    {
        $postData = $request->request->all();
        $data = $postData['data'];
        //$em = $this->getDoctrine()->getManager();

        //$racerRepository = $em->getRepository('AppBundle:Racer');
        $racer = $racerRepository->find($data['racerid']);

        //$teamRepository = $em->getRepository('AppBundle:Team');
        $team = $teamRepository->find($data['teamid']);

        try {
            //$timingRepository = $em->getRepository('AppBundle:Timing');
            $latestTeamTiming = $timingRepository->getLatestTeamTiming($team, 1);
            $previousClock = clone $latestTeamTiming->getClock();
        // FIXME no result exception
        } catch(NoResultException $e) {
            //$raceRepository = $em->getRepository('AppBundle:Race');
            $race = $raceManager->get();
            $previousClock = clone $race->getStart();
        }

        $timingId = $data['timing'];

        $timing = $timingRepository->find($timingId);
        $now = new \DateTime();

        $intervalT = $previousClock->diff($now);
        $t = new \Datetime('today '.$intervalT->format('%H:%I:%S'));

        //$timing = new Timing();
        $timing
            ->setCreatedAt($now)
            ->setRacer($racer)
            ->setIsRelay(0)
            ->setClock($now)
            ->setManual()
            ->setTiming($t)
            ;
        $em->persist($timing);
        $em->flush();

        return new JsonResponse(array('id' => $timing->getId()));
    }

    /**
     * Annule un départ
     *
     * @Route("/timing/revert-departure", name="timing_revert_departure")
     * @Method("POST")
     */
    #[Route("/timing/revert-departure", name: "timing_revert_departure", methods: ['POST'])]
    public function revertDeparture(
        TimingRepository $timingRepository,
        EntityManagerInterface $em,
        LoggerInterface $logger,
        Request $request,
    )
    {
        $timingId = $request->request->get('timing');

        $timing = $timingRepository->find($timingId);
        $timing
            ->setPrediction()
            ->setTiming(null)
            ;
        $em->persist($timing);
        $em->flush();
        $logger->info(sprintf('reset timing id %d to prediction for racer "%s"', $timing->getId(), $timing->getRacer()->getNickname()));

        return new Response();
    }
}
