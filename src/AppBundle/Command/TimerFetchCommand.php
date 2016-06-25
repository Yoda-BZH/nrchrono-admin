<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class TimerFetchCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('timer:fetch')
            ->setDescription('Fetch timers')
            ->addArgument('provider', InputArgument::REQUIRED, 'Provider to use')
            //->addOption('yell', null, InputOption::VALUE_NONE, 'Si définie, la tâche criera en majuscules')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $providerName = $input->getArgument('provider');
        $verbose = $input->getOption('verbose');

        try {
            $provider = $this->getContainer()->get(sprintf('timer.provider.%s', $providerName));
        } catch(ServiceNotFoundException $e) {
            $output->writeln(sprintf('<error>Provider "%s" unknown.</error>', $providerName));
            return 1;
        }

        $timer = $this->getContainer()->get('timer.timer');
        $timer->setProvider($provider);
        $timer->setIo($input, $verbose ? $output : null);

        return $timer->run();
    }
}
