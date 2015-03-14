<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FakerPauseUpdateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('faker:Pause:update')
            ->setDescription('Set the pauses to today')
            //->addArgument('team', InputArgument::REQUIRED, 'team to use')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();

        $repoPause = $em->getRepository('AppBundle:Pause');

        $pauses = $repoPause->findAll();
        foreach($pauses as $pause) {
            $pause->getHourStart()->modify('+1 day');
            $pause->getHourStop()->modify('+1 day');
            $em->persist($pause);
        }

        $em->flush();

        return 0;
    }
}
