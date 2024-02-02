<?php

namespace App\Service;
#use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;

use App\Repository\RaceRepository;

use App\Entity\Race;

class RaceManager {

    private $id = null;
    private $race = null;

    public function __construct(
        private RaceRepository $raceRepository
    ) {

    }

    /**
     * description
     *
     * @param void
     * @return void
     */
    public function setId($id)
    {
        $this->id = (int) $id;

        return $this;
    }

    /**
     * description
     *
     * @param void
     * @return void
     */
    public function get() :?Race
    {
        if ($this->race)
        {
            return $this->race;
        }

        $this->race = $this->raceRepository->getCurrentRace();

        return $this->race;
    }

    //public function getRaceWithTeams(bool $guest = true) :?Race
    //{
    //    var_dump($this->race);
    //    if ($this->race && count($this->race->getTeams()))
    //    {
    //        var_dump("already exists"); die();
    //        return $this->race;
    //    }
    //
    //    var_dump("new race");
    //    $this->race = $this->raceRepository->getCurrentRaceWithTeams($guest);
    //
    //    return $this->race;
    //}

    public function getRaceWithTeamsAndRacers(bool $guest = true) :?Race
    {
        if ($this->race && count($this->race->getTeams()))
        {
            return $this->race;
        }

        $this->race = $this->raceRepository->getCurrentRaceWithTeamsAndRacers($guest);

        return $this->race;
    }

    /**
     * description
     *
     * @param void
     * @return void
     */
    public function isStarted()
    {
        $now = new \Datetime();

        $race = $this->get();

        $difference = $now->diff($race->getStart());

        return $difference->invert;
    }


}
