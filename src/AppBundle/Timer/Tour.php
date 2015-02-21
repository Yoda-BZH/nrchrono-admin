<?php

namespace AppBundle\Timer;

class Tour {

    private $duree = 0;
    private $isRelai = false;
    private $isBestTime = false;

    /**
     * description
     *
     * @param void
     * @return void
     */
    public function setDuree($duree)
    {
        $this->duree = $duree;

        return $this;
    }

    /**
     * description
     *
     * @param void
     * @return void
     */
    public function setRelai($relai)
    {
        $this->isRelai = (boolean) $relai;

        return $this;
    }

    /**
     * description
     *
     * @param void
     * @return void
     */
    public function setBestTime($bestTime)
    {
        $this->isBestTime = $bestTime;

        return $this;
    }
}
