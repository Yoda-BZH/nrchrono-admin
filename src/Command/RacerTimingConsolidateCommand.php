<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;

use App\Repository\RacerRepository;
use App\Repository\TimingRepository;

use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

#[AsCommand(
    name: 'racer:timing:consolidate',
    description: 'Consolidate racer\'s timings',
    hidden: false,
    aliases: []
)]
class RacerTimingConsolidateCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        private RacerRepository $racerRepository,
        private TimingRepository $timingRepository
    )
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Consolidate racer\'s timings')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $racers = $this->racerRepository->getAll();
        foreach($racers as $racer)
        {
            //$output->writeln('Consolidating '.$racer->getNickname());
            $stats = $this->timingRepository->getStats($racer);
            if (!$stats || $stats[1] == null)
            {
                $output->writeln(sprintf('It seems %s has no timing, skipping', $racer->getNickname()));
                continue;
            }
            //var_dump($stats);
            $tMin = new \Datetime($stats[1]);
            $tMax = new \Datetime($stats[2]);
            $tAvg = new \Datetime('00:00:00');

            $tAvg->modify(sprintf('+%d seconds', $stats[3]));
            $racer->setTimingMin($tMin);
            $racer->setTimingMax($tMax);
            $racer->setTimingAvg($tAvg);

            $this->em->persist($racer);
            $this->em->flush();
            $output->writeln(sprintf('Saving timings for %s', $racer->getNickname()));
        }

        return Command::SUCCESS;
    }
}
