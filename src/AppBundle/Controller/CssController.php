<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class CssController extends Controller
{
    private $list = array(
            'default' => '',
            'darkly'  => 'bundles/app/css/bootstrap.theme.darkly.min.css',
            'slate'   => 'bundles/app/css/bootstrap.theme.slate.min.css',
        );
    /**
     *
     * @Route("/css/list", name="css_list")
     * @Template("AppBundle:Css:index.html.twig")
     */
    public function indexAction()
    {
        $session = $this->getRequest()->getSession();
        $session->set('theme_previous', $this->getRequest()->headers->get('referer'));
        return array(
            'list' => array_keys($this->list),
        );
    }

    /**
     * @Route("/css/set/{name}", name="css_set")
     */
    public function setAction($name) {
        if (!in_array($name, array_keys($this->list)))
        {
            return $this->redirect($this->generateUrl('homepage'));
        }

        $session = $this->getRequest()->getSession();
        if ($name == 'default')
        {
            $url = null;
        }
        else
        {
            $url = $this->list[$name];
        }

        $session->set('theme', $url);

        if($session->has('theme_previous')) {
            $next = $session->get('theme_previous');
            $session->remove('theme_previous');
            return $this->redirect($next);
        }

        return $this->redirect($this->generateUrl('homepage'));
    }
}
