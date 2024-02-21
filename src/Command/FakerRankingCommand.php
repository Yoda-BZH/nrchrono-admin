<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use App\Entity\Ranking;
use App\Repository\TeamRepository;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

#[AsCommand(
    name: 'faker:ranking',
    description: 'Create new timing for a team',
    hidden: false,
    aliases: []
)]
class FakerRankingCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        private TeamRepository $teamRepository,
    )
    {
        parent::__construct();
    }
    protected function configure(): void
    {
        $this
            ->setDescription('Create new timing for a team')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $teams = $this->teamRepository->findAll();

        foreach($teams as $team)
        {
            $position = 30 + $team->getId() * 10 + rand(0, 50);
            $ranking = new Ranking();
            $ranking
                ->setTeam($team)
                ->setPosition($position)
                ->setCreatedAt(new \Datetime())
                ->setTime(new \Datetime())
                ->setTour(1)
                ->setEcart(
                    new \Datetime(
                        sprintf(
                            '00:%02d:%0d',
                            rand(3,  4),
                            rand(0, 59)
                        )
                    )
                )
                ->setDistance(1)
                ->setSpeed(rand(15, 30))
                ->setBestlap(new \Datetime())
                ->setPoscat($position - 20)
                ;
            $this->em->persist($ranking);
        }

        $this->em->flush();

        return Command::SUCCESS;
    }
}
