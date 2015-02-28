<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class TeamColorExportCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('team:color')
            ->setDescription('Fetch timers')
            //->addArgument('provider', InputArgument::REQUIRED, 'Provider to use')
            //->addOption('yell', null, InputOption::VALUE_NONE, 'Si définie, la tâche criera en majuscules')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();

        $teams = $em->getRepository('AppBundle:Team')->findAll();

        $colors = array();

        $declarationBgTemplate = '.team-bg-color-%d { background-color: %s; }'.PHP_EOL;
        $declarationFgTemplate = '.team-color-%d { color: %s; }'.PHP_EOL;
        
        foreach($teams as $team) {
            if(!$team->getColor()) {
                continue;
            }
            $colors[] = sprintf($declarationBgTemplate,
                $team->getId(),
                $team->getColor()
            );
            
            $colors[] = sprintf($declarationFgTemplate,
                $team->getId(),
                $team->getColor()
            );
                
        }

        return file_put_contents('web/teams.css', implode('', $colors)) > 0;

        
    }
}
