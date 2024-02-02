<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use App\Entity\Timing;
use App\Repository\TimingRepository;

use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

#[AsCommand(
    name: 'faker:timing:cleanup',
    description: 'Delete old cleanups',
    hidden: false,
    aliases: []
)]
class FakerTimingCleanupCommand extends Command
{
    public function __construct(
        private TimingRepository $timingRepository
    )
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Delete old cleanups, older than 2 days')
            //->addArgument('team', InputArgument::REQUIRED, 'team to use')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->timingRepository
            ->createQueryBuilder('t')
            ->delete()
            ->where('t.createdAt < :date')
            ->setParameter('date', date('Y-m-d H:i:s', time() - (3600 *48)))
            ->getQuery()
            ->execute()
            ;

        return Command::SUCCESS;
    }
}
