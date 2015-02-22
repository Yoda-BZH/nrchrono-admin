<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class TimingRacerGeneratorCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('racer:timing:generator')
            ->setDescription('Create new timing for a team')
            ->addArgument('team', InputArgument::REQUIRED, 'team to use')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $teamId = $input->getArgument('team');

        $em = $this->getContainer()->get('doctrine')->getManager();

        $repoTeam = $em->getRepository('AppBundle:Team');
        $repoTiming = $em->getRepository('AppBundle:Timing');
        $repoRacer = $em->getRepository('AppBundle:Racer');

        $team = $repoTeam->find($teamId);

        $latestRacer = $repoTiming->getLatestRacer($teamId);

        $position = $latestRacer->getPosition();

        $nextRacer = $repoRacer->getNextRacerAvailable($team, $position);

        var_dump($nextRacer->getNickname(), $nextRacer->getPosition());

        //$racer = $repoTeam->getNextRacer($teamId);
    }
}
