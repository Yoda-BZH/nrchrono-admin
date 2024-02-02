<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

#[AsCommand(
    name: 'dashing:message',
    description: '??',
    hidden: false,
    aliases: ['app:add-user']
)]
class DashingMessageCommand extends Command
{
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

        $url = 'http://localhost:3030/widgets/message';
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(array(
            "auth_token" => "ttrtyuijk",
            "text" => $message,
        )));
        curl_exec($curl);

        return Command::SUCCESS;
    }
}
