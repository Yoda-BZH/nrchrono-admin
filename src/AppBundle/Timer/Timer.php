<?php

namespace AppBundle\Timer;

use AppBundle\Timer\Provider\Provider;
use Doctrine\ORM\EntityManager;

class Timer {

    private $provider;

    private $em;

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
    public function setProvider(Provider $provider)
    {
        $this->provider = $provider;

        return $this;
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


    /**
     * description
     *
     * @param void
     * @return void
     */
    public function run()
    {
        $r = $this->provider->getGeneral();

        var_dump($r);

        return 0;
    }

}
