<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use App\Repository\TeamRepository;
use App\Service\RaceManager;
use App\Service\NextRacerGuesser;

use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

#[AsCommand(
    name: 'dashing:racer:running',
    description: 'Pushing currently running racers to dashing',
    hidden: false,
    aliases: []
)]
class DashingRacerRunningCommand extends Command
{
    public function __construct(
        private TeamRepository $teamRepository,
        private RaceManager $raceManager,
        private NextRacerGuesser $nextGuesser
    )
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Racers running')
            //->addArgument('team', InputArgument::REQUIRED, 'team to use')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $verbose = $input->getOption('verbose');

        $raceIsStarted = $this->raceManager->isStarted();

        $teams = $this->teamRepository->findAll();
        $now = new \Datetime();
        //$nextGuesser = $this->getContainer()->get('racer.next');

        $racers = array();
        $preparing = array();
        foreach($teams as $team)
        {
            if(!$raceIsStarted)
            {
                $preparing[] = array('label' => $team->getName(), 'value' => 'En attente');
                continue;
            }
            $nextRacers = $this->nextGuesser
                ->setTeam($team)
                ->getNexts()
                ;
            $nextRacer = $nextRacers[0];

            $racers[] = array('label' => $team->getName(), 'value' => $nextRacer->getNickname());
            if(isset($nextRacers[1]))
            {
                $preparing[] = array('label' => $team->getName(), 'value' => $nextRacers[1]->getNickname());
            }
        }


        $url = 'http://localhost:3030/widgets/sur-la-piste';
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(array(
            "auth_token" => "ttrtyuijk",
            "items" => $racers,
        )));
        curl_exec($curl);

        $url = 'http://localhost:3030/widgets/preparation';
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(array(
            "auth_token" => "ttrtyuijk",
            "items" => $preparing,
        )));
        curl_exec($curl);

        return Command::SUCCESS;
    }
}
