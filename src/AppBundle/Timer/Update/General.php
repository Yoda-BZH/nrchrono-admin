<?php

namespace AppBundle\Timer\Update;

class General {

    private $teams = array();
    private $ids   = array();

    /**
     * description
     *
     * @param void
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * description
     *
     * @param void
     * @return void
     */
    public function setTeams($teams)
    {
        $this->teams = $teams;

        return $this;
    }

    /**
     * description
     *
     * @param void
     * @return void
     */
    public function setTrackedTeams(array $ids)
    {
        $this->ids = $ids;

        return $this;
    }

}
