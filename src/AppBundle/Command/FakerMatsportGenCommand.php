<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use AppBundle\Entity\Timing;

use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class FakerMatsportGenCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('faker:matsport:gen')
            ->setDescription('Faker for matsport stats generation')
            //->addArgument('team', InputArgument::REQUIRED, 'team to use')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $rootDir = $container->getParameter('kernel.root_dir');
        $file = $rootDir . DIRECTORY_SEPARATOR . 'matsport.html';

        $twig = $container->get('twig');

        $template = $twig->loadTemplate('AppBundle:Matsport:main.html.twig');

        $data = array();

        $em = $cotainer->get('doctrine')->getManager();

        $repoTeams = $em->getRepository('AppBundle:Team');
        $teams = $repoTeams->findAll();

        $repoMatsport = $em->getRepository('AppBundle:Matsport');


        foreach($teams as $team) {
            $lastTiming = $repoMatsport->findLatestForTeam($team->getId());
            $data[$team->getId()] = array(
                'team' => $team,
                'timing' => $lastTiming,
            );
        }

        $data = $twig->render($template, $data);

        return 0;
    }
}
