<?php

namespace AppBundle\Service;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;

class Race {

    private $id = null;
    private $race = null;
    private $em;
    private $repoRace = null;

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

        $this->repoRace = $this->em->getRepository('AppBundle:Race');

        return $this;
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
    public function get()
    {
        if ($this->race)
        {
            return $this->race;
        }

        if (!$this->id)
        {
            throw new \UnexptectedException('No race id value given');
        }

        return $this->race = $this->repoRace->find($this->id); // FIXME
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
