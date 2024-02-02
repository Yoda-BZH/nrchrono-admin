<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\NoResultException;

use App\Repository\TeamRepository;
use App\Repository\TimingRepository;
use App\Service\RaceManager;

use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

#[AsCommand(
    name: 'dashing:latestlaps',
    description: 'Latests laps per team',
    hidden: false,
    aliases: []
)]
class DashingLatestlapsCommand extends Command
{
    public function __construct(
        private TeamRepository $teamRepository,
        private TimingRepository $timingRepository,
        private RaceManager $raceManager
    )
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Latests laps per team')
            //->addArgument('team', InputArgument::REQUIRED, 'team to use')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $verbose = $input->getOption('verbose');

        $raceIsStarted = $this->raceManager->isStarted();

        $data = array();

        foreach($this->teamRepository->findAll() as $team)
        {
            if(!$raceIsStarted)
            {
                $data[$team->getId()] = array(
                    'label' => sprintf('%s', str_replace('NR-', '', $team->getName())),
                    'value' => '--:--',
                );
                continue;
            }
            try {
                $timing = $this->timingRepository->getLatestTeamLap($team);
            } catch(NoResultException $e) {
                $data[$team->getId()] = array(
                    'label' => sprintf('%s', str_replace('NR-', '', $team->getName())),
                    'value' => 'Aucun tour',
                );
                continue;
            }
            $data[$timing['teamid']] = array(
                'label' => sprintf('%s (%s)', $timing['nickname'], str_replace('NR-', '', $timing['name'])),
                'value' => $timing['timing']->format('H:i:s'),
            );
        }

        ksort($data);
        $data = array_values($data);
        //$repoRanking = $em->getRepository('AppBundle:Ranking');

        //$teams = $this->teamRepository->findAll();

        #$rankingGeneral = array();
        #$rankingCategory = array();
        #foreach($teams as $team)
        #{
        #    $ranking = $repoRanking->getLatestRankingForTeam($team);
        #    if(!$ranking) {
        #        continue;
        #    }
        #
        #    $rankingGeneral[] = array(
        #        'label' => $team->getName(),
        #        'value' => $ranking->getPosition()
        #    );
        #
        #    $rankingCategory[] = array(
        #        'label' => $team->getName(),
        #        'value' => $ranking->getPoscat()
        #    );
        #}
        #
        #if(!$rankingGeneral) {
        #    return 0;
        #}

        $url = 'http://localhost:3030/widgets/latestlaps';
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(array(
            "auth_token" => "ttrtyuijk",
            "items" => $data,
        )));
        curl_exec($curl);

        return Command::SUCCESS;
    }
}
