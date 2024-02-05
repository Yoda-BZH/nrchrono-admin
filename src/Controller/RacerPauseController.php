<?php

namespace App\Controller;

use App\Entity\RacerPause;
use App\Form\RacerPauseType;
use App\Repository\RacerPauseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/racerpause')]
class RacerPauseController extends AbstractController
{
    #[Route('/', name: 'racerpause', methods: ['GET'])]
    public function index(RacerPauseRepository $racerPauseRepository): Response
    {
        return $this->render('racer_pause/index.html.twig', [
            'racer_pauses' => $racerPauseRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'racer_pause_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $racerPause = new RacerPause();
        $form = $this->createForm(RacerPauseType::class, $racerPause);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager->persist($racerPause);
            $entityManager->flush();

            return $this->redirectToRoute('racerpause', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('racer_pause/new.html.twig', [
            'racer_pause' => $racerPause,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'racer_pause_show', methods: ['GET'])]
    public function show(RacerPause $racerPause): Response
    {
        return $this->render('racer_pause/show.html.twig', [
            'racer_pause' => $racerPause,
        ]);
    }

    #[Route('/{id}/edit', name: 'racer_pause_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, RacerPause $racerPause, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RacerPauseType::class, $racerPause);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager->flush();

            return $this->redirectToRoute('racerpause', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('racer_pause/edit.html.twig', [
            'racer_pause' => $racerPause,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'racer_pause_delete', methods: ['POST'])]
    public function delete(Request $request, RacerPause $racerPause, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$racerPause->getId(), $request->request->get('_token')))
        {
            $entityManager->remove($racerPause);
            $entityManager->flush();
        }

        return $this->redirectToRoute('racerpause', [], Response::HTTP_SEE_OTHER);
    }
}
