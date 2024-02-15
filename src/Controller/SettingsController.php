<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\TeamRepository;

#[Route('/settings')]
class SettingsController extends AbstractController
{
    private $list = array(
            'default' => '',
            'cerulean'   => '/css/bootstrap.theme.cerulean.min.css',
            'cosmo'   => '/css/bootstrap.theme.cosmo.min.css',
            'cyborg'   => '/css/bootstrap.theme.cyborg.min.css',
            'darkly'   => '/css/bootstrap.theme.darkly.min.css',
            'flatly'   => '/css/bootstrap.theme.flatly.min.css',
            'journal'   => '/css/bootstrap.theme.journal.min.css',
            'litera'   => '/css/bootstrap.theme.litera.min.css',
            'lumen'   => '/css/bootstrap.theme.lumen.min.css',
            'lux'   => '/css/bootstrap.theme.lux.min.css',
            'materia'   => '/css/bootstrap.theme.materia.min.css',
            'minty'   => '/css/bootstrap.theme.minty.min.css',
            'morph'   => '/css/bootstrap.theme.morph.min.css',
            'pulse'   => '/css/bootstrap.theme.pulse.min.css',
            'quartz'   => '/css/bootstrap.theme.quartz.min.css',
            'sandstone'   => '/css/bootstrap.theme.sandstone.min.css',
            'simplex'   => '/css/bootstrap.theme.simplex.min.css',
            'sketchy'   => '/css/bootstrap.theme.sketchy.min.css',
            'slate'   => '/css/bootstrap.theme.slate.min.css',
            'solar'   => '/css/bootstrap.theme.solar.min.css',
            'spacelab'   => '/css/bootstrap.theme.spacelab.min.css',
            'superhero'   => '/css/bootstrap.theme.superhero.min.css',
            'united'   => '/css/bootstrap.theme.united.min.css',
            'vapor'   => '/css/bootstrap.theme.vapor.min.css',
            'yeti'   => '/css/bootstrap.theme.yeti.min.css',
            'zephyr'   => '/css/bootstrap.theme.zephyr.min.css',

        );
    /**
     *
     * @Route("/css/list", name="css_list")
     * @Template("AppBundle:Css:index.html.twig")
     */
    #[Route("/", name: "settings_list")]
    public function indexAction(
        Request $request,
        TeamRepository $teamRepository,
    )
    {
        $session = $request->getSession();
        $session->set('theme_previous', $request->headers->get('referer'));

        $session = $request->getSession();
        $nb_after = $session->get('pref_status_list_team_after', 3);
        $nb_before = $session->get('pref_status_list_team_before', 5);

        $teams = $teamRepository->getAll();
        $team_filter = $session->get('team_filter', 0);

        $currentTheme = array_search($session->get('theme'), $this->list);

        return $this->render("Settings/index.html.twig", array(
            'css_list' => $this->list,
            'nb_after' => $nb_after,
            'nb_before' => $nb_before,
            'teams' => $teams,
            'team_filter' => $team_filter,
            'current_theme' => $currentTheme,
        ));
    }

    /**
     * @Route("/css/set/{name}", name="css_set")
     */
    #[Route('/css/set/{name}', name: "css_set", methods: ['GET'])]
    public function setAction($name, Request $request)
    {
        if (!in_array($name, array_keys($this->list)))
        {
            return $this->redirect($this->generateUrl('homepage'));
        }

        $session = $request->getSession();
        if ($name == 'default')
        {
            $url = null;
        }
        else
        {
            $url = $this->list[$name];
        }

        $session->set('theme', $url);

        if($session->has('theme_previous'))
        {
            $next = $session->get('theme_previous');
            $session->remove('theme_previous');
            return $this->redirect($next);
        }

        return $this->redirect($this->generateUrl('homepage'));
    }

    #[Route("/status/after/{nb}", name: "settings_status_list_team_after", methods: ['GET'])]
    public function setStatusAfter(
        $nb,
        Request $request,
    ): Response
    {
        if ($nb > 3 or $nb < 1)
        {
            return $this->redirectToRoute('settings_list');
        }

        $session = $request->getSession();
        $session->set('pref_status_list_team_after', $nb);

        return $this->redirectToRoute('settings_list');
    }

    #[Route("/status/before/{nb}", name: "settings_status_list_team_before", methods: ['GET'])]
    public function setStatusBefore(
        $nb,
        Request $request,
    ): Response
    {
        if ($nb > 5 or $nb < 1)
        {
            return $this->redirectToRoute('settings_list');
        }

        $session = $request->getSession();
        $session->set('pref_status_list_team_before', $nb);

        return $this->redirectToRoute('settings_list');
    }

    #[Route("/team-filter/{id}", name: "settings_team_filter", methods: ['GET'])]
    public function setFilterTeams(
        int $id,
        Request $request,
        TeamRepository $teamRepository,
    ): Response
    {
        $teams = $teamRepository->getAll();

        $session = $request->getSession();

        if ($id == 0)
        {
            $session->remove('team_filter');
        }
        else
        {
            $team_found = false;
            foreach($teams as $team)
            {
                if ($team->getId() == $id)
                {
                    $team_found = true;
                    break;
                }
            }
            if ($team_found)
            {
                $session->set('team_filter', $id);
            }
        }

        return $this->redirectToRoute('settings_list');
    }
}
