<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Timing
 *
 * @ORM\Table(name="timing", indexes={@ORM\Index(name="fk_timing_1_idx", columns={"id_racer"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TimingRepository")
 */
class Timing
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="timing", type="time", nullable=true)
     */
    private $timing;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_relay", type="boolean", nullable=true)
     */
    private $isRelay;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="clock", type="datetime", nullable=true)
     */
    private $clock;

    /**
     * @var \Racer
     *
     * @ORM\ManyToOne(targetEntity="Racer", inversedBy="timings")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_racer", referencedColumnName="id")
     * })
     */
    private $idRacer;



    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set timing
     *
     * @param integer $timing
     * @return Timing
     */
    public function setTiming($timing)
    {
        $this->timing = $timing;

        return $this;
    }

    /**
     * Get timing
     *
     * @return integer
     */
    public function getTiming()
    {
        return $this->timing;
    }

    /*public function getTimingToSec() {
        $min = $this->timing->format('i');
        $sec = $this->timing->format('s');
        return $min * 60 + $sec;
    }*/

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Timing
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get clock
     *
     * @return \DateTime
     */
    public function getClock()
    {
        return $this->clock;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $clock
     * @return Timing
     */
    public function setClock($clock)
    {
        $this->clock = $clock;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set isRelay
     *
     * @param boolean $isRelay
     * @return Timing
     */
    public function setIsRelay($isRelay)
    {
        $this->isRelay = $isRelay;

        return $this;
    }

    /**
     * Get isRelay
     *
     * @return boolean
     */
    public function getIsRelay()
    {
        return $this->isRelay;
    }

    /**
     * Set idRacer
     *
     * @param \AppBundle\Entity\Racer $idRacer
     * @return Timing
     */
    public function setIdRacer(\AppBundle\Entity\Racer $idRacer = null)
    {
        $this->idRacer = $idRacer;

        return $this;
    }

    /**
     * Get idRacer
     *
     * @return \AppBundle\Entity\Racer
     */
    public function getIdRacer()
    {
        return $this->idRacer;
    }

    public function getTimingToSec() {
        $hours = $this->timing->format('H');
        $minutes = $this->timing->format('i');
        $secondes = $this->timing->format('s');
        return $hours * 3600 + $minutes * 60 + $secondes;
    }
}
