<?php

namespace AppBundle\Service;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;

class NextRacerGuesser {
    
    private $em;
    private $team;
    
    public function __construct() {
        
    }
    
    

    /**
     * Set the value of
     *
     *
     */
    public function setEm(EntityManager $em)
    {
        $this->em = $em;

        return $this;
    }
    
    public function setTeam($team) {
        $this->team = $team;
        
        return $this;
    }

    public function getNext() {
        $repoTiming = $this->em->getRepository('AppBundle:Timing');
        
        try {
            $latestRacer = $repoTiming->getLatestRacer($this->team->getId());
        } catch(NoResultException $e) {
            return null;
        }
        var_dump('latest racer', $latestRacer->getNickname(), $latestRacer->getPosition());
        
        $position = $latestRacer->getPosition();
        //var_dump('latest', $position, $latestRacer->getNickname());
        
        $repoRacer = $this->em->getRepository('AppBundle:Racer');
        //$output->writeln('<info>getting next racer available</info>');
        try {
            $nextRacer = $repoRacer->getNextRacerAvailable($this->team, $position);
        } catch(NoResultException $e) {
            return null;
        }

        return $nextRacer;
    }
}
