<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Doctrine\ORM\EntityManagerInterface;

use App\Repository\PauseRepository;

#[AsCommand(
    name: 'faker:pause:update',
    description: 'Set the pauses to today',
    hidden: false,
    aliases: []
)]
class FakerPauseUpdateCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        private PauseRepository $pauseRepository
    )
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Set the pauses to today')
            //->addArgument('team', InputArgument::REQUIRED, 'team to use')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $pauses = $this->pauseRepository->findAll();
        foreach($pauses as $pause) {
            $pause->getHourStart()->modify('+1 day');
            $pause->setHourStart(clone $pause->getHourStart());
            $pause->getHourStop()->modify('+1 day');
            $pause->setHourStop(clone $pause->getHourStop());
            $this->em->persist($pause);
        }

        $this->em->flush();

        return Command::SUCCESS;
    }
}
