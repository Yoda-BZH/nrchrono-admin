<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(): Response
    {
        $dashboardPaddock = $this->getParameter('dashing.dashboard.paddock');
        $dashboardTrack = $this->getParameter('dashing.dashboard.track');

        return $this->render('homepage.html.twig', array(
            'link_paddock' => $dashboardPaddock,
            'link_track' => $dashboardTrack,
        ));
    }
}
