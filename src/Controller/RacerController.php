<?php

namespace App\Controller;

use App\Entity\Racer;
use App\Entity\Team;
use App\Form\Racer1Type;
use App\Repository\RacerRepository;
use App\Repository\TeamRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/racer')]
class RacerController extends AbstractController
{
    #[Route('/', name: 'racer_index', methods: ['GET'])]
    public function index(RacerRepository $racerRepository): Response
    {
        $racers = $racerRepository->getAllByTeam(guest: true);
        $teams = array();

        foreach($racers as $racer)
        {
            $teamId = $racer->getTeam()->getId();
            if (!isset($teams[$teamId]))
            {
                $teams[$teamId] = $racer->getTeam();
            }
        }

        return $this->render('racer/index.html.twig', [
            'selected' => 0,
            'racers' => $racers,
            'teams' => $teams,
        ]);
    }

    #[Route('/team/{id}', name: 'racer_index_by_team', methods: ['GET'])]
    public function indexByTeam(
        //TeamRepository $teamRepository,
        RacerRepository $racerRepository,
        $id
    ): Response
    {
        $racers = $racerRepository->getAllByTeam(guest: true, team: $id);

        return $this->render('racer/order.html.twig', [
            'selected' => $id,
            'racers' => $racers,
        ]);
    }

    #[Route('/new', name: 'racer_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $racer = new Racer();
        $form = $this->createForm(Racer1Type::class, $racer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager->persist($racer);
            $entityManager->flush();

            return $this->redirectToRoute('racer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('racer/new.html.twig', [
            'racer' => $racer,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'racer_show', methods: ['GET'])]
    public function show(Racer $racer): Response
    {
        return $this->render('racer/show.html.twig', [
            'racer' => $racer,
        ]);
    }

    #[Route('/{id}/edit', name: 'racer_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Racer $racer, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Racer1Type::class, $racer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager->flush();

            return $this->redirectToRoute('racer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('racer/edit.html.twig', [
            'racer' => $racer,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'racer_delete', methods: ['POST'])]
    public function delete(Request $request, Racer $racer, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$racer->getId(), $request->request->get('_token')))
        {
            $entityManager->remove($racer);
            $entityManager->flush();
        }

        return $this->redirectToRoute('racer_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route("/update-order/{id}", name: "racer_update_order", methods: ['POST'])]
    public function updateOrder(
        EntityManagerInterface $entityManager,
        RacerRepository $racerRepository,
        Request $request,
        Team $team,
    ): Response
    {
        $postData = $request->getPayload()->all();
        $newOrder = $postData['order'];

        $racers = $racerRepository->getAllByTeam(guest: true, team: $team->getId());
        $racersById = array();
        foreach($racers as $racer)
        {
            $racersById[$racer->getId()] = $racer;
        }

        foreach($newOrder as $pos => $racerId)
        {
            $racer = $racersById[$racerId];
            $racer->setPosition($pos + 1);
            $entityManager->persist($racer);
        }

        //$racer->setPosition($newPosition);
        //$entityManager->persist($racer);
        $entityManager->flush();

        return new Response();
    }
}
