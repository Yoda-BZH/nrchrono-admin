<?php

namespace App\Controller;

use App\Entity\Racer;
use App\Form\Racer1Type;
use App\Repository\RacerRepository;
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
        return $this->render('racer/index.html.twig', [
            'racers' => $racerRepository->getAllByTeam(),
        ]);
    }

    #[Route('/new', name: 'racer_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $racer = new Racer();
        $form = $this->createForm(Racer1Type::class, $racer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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

        if ($form->isSubmitted() && $form->isValid()) {
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
        if ($this->isCsrfTokenValid('delete'.$racer->getId(), $request->request->get('_token'))) {
            $entityManager->remove($racer);
            $entityManager->flush();
        }

        return $this->redirectToRoute('racer_index', [], Response::HTTP_SEE_OTHER);
    }
}
