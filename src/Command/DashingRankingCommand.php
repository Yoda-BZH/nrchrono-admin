<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use App\Repository\TeamRepository;
use App\Repository\RankingRepository;
use App\Service\RaceManager;

use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

#[AsCommand(
    name: 'dashing:ranking',
    description: 'Push rankings to dashing',
    hidden: false,
    aliases: []
)]
class DashingRankingCommand extends Command
{
    public function __construct(
        private TeamRepository $teamRepository,
        private RankingRepository $rankingRepository,
        private RaceManager $raceManager
    )
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Ranking')
            //->addArgument('team', InputArgument::REQUIRED, 'team to use')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $verbose = $input->getOption('verbose');

        $raceIsStarted = $this->raceManager->isStarted();

        $teams = $this->teamRepository->findAllWithGuest();

        $rankingGeneral = array();
        $rankingCategory = array();
        foreach($teams as $team)
        {
            if (!$raceIsStarted)
            {
                $rankingGeneral[] = array(
                    'label' => $team->getName(),
                    'value' => 'En attente départ'
                );
                $rankingCategory[] = array(
                    'label' => $team->getName(),
                    'value' => 'En attente départ'
                );
                continue;
            }

            $ranking = $this->rankingRepository->getLatestRankingForTeam($team);
            if(!$ranking)
            {
                $rankingGeneral[] = array(
                    'label' => $team->getName(),
                    'value' => 'N/A'
                );
                $rankingCategory[] = array(
                    'label' => $team->getName(),
                    'value' => 'N/A'
                );
                continue;
            }

            $rankingGeneral[] = array(
                'label' => $team->getName(),
                'value' => $ranking->getPosition()
            );

            $rankingCategory[] = array(
                'label' => $team->getName(),
                'value' => $ranking->getPoscat()
            );
        }

        if(!$rankingGeneral)
        {
            return 0;
        }

        $url = 'http://localhost:3030/widgets/rankingGen';
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(array(
            "auth_token" => "ttrtyuijk",
            "items" => $rankingGeneral,
        )));
        curl_exec($curl);

        $url = 'http://localhost:3030/widgets/rankingCat';
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(array(
            "auth_token" => "ttrtyuijk",
            "items" => $rankingCategory,
        )));
        curl_exec($curl);

        return Command::SUCCESS;
    }
}
