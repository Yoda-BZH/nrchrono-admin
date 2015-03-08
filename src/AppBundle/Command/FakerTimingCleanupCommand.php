<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use AppBundle\Entity\Timing;

use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class FakerTimingCleanupCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('faker:timing:cleanup')
            ->setDescription('Delete old cleanups')
            //->addArgument('team', InputArgument::REQUIRED, 'team to use')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();

        $repoTiming = $em->getRepository('AppBundle:Timing');

        $repoTiming
            ->createQueryBuilder('t')
            ->delete()
            ->where('t.createdAt < :date')
            ->setParameter('date', date('Y-m-d H:i:s', time() - (3600 *48)))
            ->getQuery()
            ->execute()
            ;

        return 0;
    }
}
