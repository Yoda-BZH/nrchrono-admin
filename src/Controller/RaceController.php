<?php

namespace App\Controller;

use App\Entity\Race;
use App\Form\Race1Type;
use App\Repository\RaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Service\NextRacerGuesser;

#[Route('/race')]
class RaceController extends AbstractController
{
    #[Route('/', name: 'race_index', methods: ['GET'])]
    public function index(RaceRepository $raceRepository): Response
    {
        return $this->render('race/index.html.twig', [
            'races' => $raceRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'race_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $race = new Race();
        $form = $this->createForm(Race1Type::class, $race);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager->persist($race);
            $entityManager->flush();

            return $this->redirectToRoute('race_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('race/new.html.twig', [
            'race' => $race,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'race_show', methods: ['GET'])]
    public function show(Race $race): Response
    {
        return $this->render('race/show.html.twig', [
            'race' => $race,
        ]);
    }

    #[Route('/{id}/edit', name: 'race_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Race $race, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Race1Type::class, $race);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager->flush();

            return $this->redirectToRoute('race_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('race/edit.html.twig', [
            'race' => $race,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'race_delete', methods: ['POST'])]
    public function delete(Request $request, Race $race, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$race->getId(), $request->request->get('_token')))
        {
            $entityManager->remove($race);
            $entityManager->flush();
        }

        return $this->redirectToRoute('race_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route("/{id}/start", name: "race_start", methods: ['GET'])]
    public function raceStart(
        Request $request,
        Race $race,
        EntityManagerInterface $em,
        NextRacerGuesser $nextRaceGuesser,
    ): Response
    {
        $now = new \DateTime();
        $race->setStart($now);
        $em->persist($race);
        $em->flush();

        $teams = $race->getTeams();

        foreach($teams as $team)
        {
            $nextRaceGuesser
                ->setTeam($team)
                ->computeNexts()
                ;
            $timings = $nextRaceGuesser->getPredictions($team->getId());
            foreach($timings as $t)
            {
                $em->remove($t);
            }
        }
        $em->flush();

        return $this->redirectToRoute('timing_status', [], Response::HTTP_SEE_OTHER);
    }
}
