<?php

namespace App\Controller;

use App\Entity\Pause;
use App\Entity\RacerPause;
use App\Form\Pause1Type;
use App\Repository\PauseRepository;
use App\Repository\RacerPauseRepository;
use App\Repository\RacerRepository;
use App\Repository\TeamRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

use App\Service\RaceManager;

#[Route('/pause')]
class PauseController extends AbstractController
{
    #[Route('/', name: 'pause', methods: ['GET'])]
    public function index(PauseRepository $pauseRepository): Response
    {
        return $this->render('pause/index.html.twig', [
            'pauses' => $pauseRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'pause_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $pause = new Pause();
        $pause->setHourStart(new \Datetime('today 00:00'));
        $pause->setHourStop(new \Datetime('today 00:00'));
        $form = $this->createForm(Pause1Type::class, $pause);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($pause);
            $entityManager->flush();

            return $this->redirectToRoute('pause', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pause/new.html.twig', [
            'pause' => $pause,
            'form' => $form,
        ]);
    }

    #[Route('/visual/create-pause', name: 'pause_visual_create_pause', methods: ['POST'])]
    public function visualCreatePause(
        Request $request,
        EntityManagerInterface $entityManager,
        TeamRepository $teamRepository,
        RacerRepository $racerRepository,
    ): Response
    {
        $pause = new Pause();
        $pause->setPorder(1);
        $pause->setHourStart(new \Datetime($request->getPayload()->get('start', '')));
        $pause->setHourStop(new \Datetime($request->getPayload()->get('stop', '')));

        $teamId = $request->getPayload()->get('team_id', 0);
        if (!$teamId)
        {
            throw $this->createNotFoundException('team id not found');
        }

        $team = $teamRepository->find($teamId);
        if (!$team)
        {
            throw $this->createNotFoundException('team not found');
        }

        $pause->setTeam($team);
        $entityManager->persist($pause);
        $entityManager->flush();

        return new JsonResponse(array('id' => $pause->getId()));
    }

    #[Route('/visual/create', name: 'pause_visual_create', methods: ['POST'])]
    public function visualCreate(
        Request $request,
        EntityManagerInterface $entityManager,
        TeamRepository $teamRepository,
        RacerRepository $racerRepository,
    ): Response
    {
        $postData = $request->getPayload()->all();
        $racerIds = $postData['racers'];
        foreach($racerIds as $racerId)
        {
            $racer = $racerRepository->find($racerId);
            $racerPause = new RacerPause();
            $racerPause
                ->setPause($pause)
                ->setRacer($racer)
                ;
            $entityManager->persist($racerPause);
        }
        $entityManager->flush();

        return new JsonResponse(array('id' => $racerPause->getId()));
    }

    #[Route("/list", name: "pause_list_json", methods: ['GET'])]
    public function listJson(
        Request $request,
        PauseRepository $pauseRepository,
        RacerPauseRepository $racerPauseRepository,
    ): Response
    {
        $events = array();
        $start = $request->query->get('start');
        $end = $request->query->get('end');
        $session = $request->getSession();
        $teamFilter = $session->get('team_filter', 0);

        $validPauses = $pauseRepository->getAllWithRacerPause($start, $end);
        foreach($validPauses as $pause)
        {
            if ($teamFilter && $teamFilter != $pause->getTeam()->getId())
            {
                continue;
            }
            $event = array(
                'id' => $pause->getId(),
                'start' => $pause->getHourStart()->format('c'),
                'end' => $pause->getHourStop()->format('c'),
                'color' => $pause->getTeam()->getColor(),
                'extendedProps' => array(
                    'team_id' => $pause->getTeam()->getId(),
                    'racer_ids' => array(),
                    'racerpause_ids' => array(),
                ),
            );
            $racers = array();
            foreach($pause->getRacerPauses() as $rp)
            {
                $racers[] = $rp->getRacer();
                $event['extendedProps']['racer_ids'][] = $rp->getRacer()->getId();
                $event['extendedProps']['racerpause_ids'][] = $rp->getId();
            }

            $title = sprintf(
                '%s: %s',
                (string) $pause->getTeam(),
                implode(', ', array_map(fn($i) => (string) $i, $racers)) ?: 'Aucun coureur'
            );
            $event['title'] = $title;

            $events[] = $event;
        }

        return new JsonResponse($events);
    }

    #[Route("/visual", name: "pause_visual", methods: ['GET'])]
    public function visual(
        RaceManager $raceManager,
        Request $request,
    ): Response
    {
        $session = $request->getSession();
        $teamFilter = $session->get('team_filter', 0);

        $race = $raceManager->getRaceWithTeamsAndRacers();

        $allTeams = $race->getTeams();
        $teams = array();
        foreach($allTeams as $t)
        {
            if ($teamFilter == 0 || $t->getId() == $teamFilter)
            {
                $teams[] = $t;
            }
        }

        $yesterday = clone $race->getStart();
        $yesterday->modify('-1 day');

        return $this->render('pause/visual.html.twig', array(
            'startDate' => $yesterday->format('Y-m-d'),
            'teams' => $teams,
        ));
    }

    #[Route('/{id}', name: 'pause_show', methods: ['GET'])]
    public function show(Pause $pause): Response
    {
        return $this->render('pause/show.html.twig', [
            'pause' => $pause,
        ]);
    }

    #[Route('/{id}/edit', name: 'pause_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Pause $pause, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Pause1Type::class, $pause);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('pause', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pause/edit.html.twig', [
            'pause' => $pause,
            'form' => $form,
        ]);
    }

    #[Route("/edit2", name: "pause_edit_js", methods: ['POST'])]
    public function edit2(
        Request $request,
        PauseRepository $pauseRepository,
        EntityManagerInterface $entityManager,
    ): Response
    {
        $pauseId = $request->getPayload()->get('id', 0);
        if (!$pauseId)
        {
            throw $this->createNotFoundException('Pas d\'id de pause');
        }

        $pause = $pauseRepository->find($pauseId);
        if (!$pause)
        {
            throw $this->createNotFoundException('Pause non trouvé');
        }
        $start = new \Datetime($request->getPayload()->get('start', ''));
        $stop = new \Datetime($request->getPayload()->get('stop', ''));
        $pause
            ->setHourStart($start)
            ->setHourStop($stop)
            ;
        $entityManager->persist($pause);
        $entityManager->flush();

        return new Response();
    }

    #[Route('/{id}', name: 'pause_delete', methods: ['POST'])]
    public function delete(Request $request, Pause $pause, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$pause->getId(), $request->request->get('_token'))) {
            $entityManager->remove($pause);
            $entityManager->flush();
        }

        return $this->redirectToRoute('pause', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/visual/delete/{pause}', name: 'pause_delete', methods: ['POST'])]
    public function deleteVisual(Request $request, Pause $pause, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($pause);
        $entityManager->flush();

        return new Response();
    }

    #[Route("/visual/edit/{id}", name: "pause_visual_edit", methods: ['POST'])]
    public function visualEdit(
        Request $request,
        $id,
        EntityManagerInterface $entityManager,
        PauseRepository $pauseRepository,
        RacerRepository $racerRepository,
    ): Response
    {
        $pause = $pauseRepository->getOneWithRacerPause($id);

        if (!$pause)
        {
            throw $this->createNotFoundException('The product does not exist');
        }

        $currentPausedRacers = array();
        foreach($pause->getRacerPauses() as $racerPause)
        {
            $currentPausedRacers[] = $racerPause->getRacer()->getId();
        }
        $postData = $request->getPayload()->all(); //'racers', null);
        $selectedRacerIds = $postData['racers'] ?? array();

        $newRacerIds = array_diff($selectedRacerIds, $currentPausedRacers);
        $deletedRacerIds = array_diff($currentPausedRacers, $selectedRacerIds);

        foreach($pause->getRacerPauses() as $racerPause)
        {
            if ($deletedRacerIds && in_array($racerPause->getRacer()->getId(), $deletedRacerIds))
            {
                $entityManager->remove($racerPause);
            }
        }

        foreach($newRacerIds as $newRacerId)
        {
            $racer = $racerRepository->find($newRacerId);
            $racerPause = new RacerPause();
            $racerPause
                ->setPause($pause)
                ->setRacer($racer)
                ;
            $entityManager->persist($racerPause);
        }
        $entityManager->flush();

        return new Response();
    }
}
