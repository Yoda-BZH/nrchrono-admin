<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use App\Service\Dashing;

use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

#[AsCommand(
    name: 'dashing:message',
    description: '??',
    hidden: false,
    aliases: ['app:add-user']
)]
class DashingMessageCommand extends Command
{
    public function __construct(
        private Dashing $dashing,
    )
    {
        parent::__construct();
    }
    protected function configure()
    {
        $this
            ->setName('dashing:message') ->setDescription('Ranking')
            ->addArgument('message', InputArgument::REQUIRED, 'team to use')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $message = $input->getArgument('message');
        $this->dashing->send('/widgets/message', array('text' => $message));

        return Command::SUCCESS;
    }
}
