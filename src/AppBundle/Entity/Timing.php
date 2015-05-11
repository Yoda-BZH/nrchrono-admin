<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Timing
 *
 * @ORM\Table(name="timing", indexes={
 *     @ORM\Index(name="fk_timing_1_idx",  columns={"id_racer"}),
 *     @ORM\Index(name="fk_timing_2_type", columns={"type"})
 * })
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TimingRepository")
 */
class Timing
{
    const AUTOMATIC  = 1;
    const MANUAL     = 2;
    const PREDICTION = 3;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \DateTime
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
     * @var boolean
     *
     * @ORM\Column(name="type", type="smallint", nullable=false, options={"default"=1})
     */
    private $type;

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
     * @param \DateTime $timing
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
     * @return \DateTime
     */
    public function getTiming()
    {
        return $this->timing;
    }

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
     * Set clock
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
     * Get clock
     *
     * @return \DateTime
     */
    public function getClock()
    {
        return $this->clock;
    }

    /**
     * Set type
     *
     * @param boolean $type
     * @return Timing
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    public function setPrediction()
    {
        return $this->setType(self::PREDICTION);
    }

    public function isPrediction() {
        return $this->type == self::PREDICTION;
    }

    public function setManual()
    {
        return $this->setType(self::MANUAL);
    }

    public function isManual() {
        return $this->type == self::MANUAL;
    }

    public function setAutomatic()
    {
        return $this->setType(self::AUTOMATIC);
    }

    public function isAutomatic() {
        return $this->type == self::AUTOMATIC;
    }

    /**
     * Get type
     *
     * @return boolean
     */
    public function getType()
    {
        return $this->type;
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

    /*public function getTimingToSec() {
        $min = $this->timing->format('i');
        $sec = $this->timing->format('s');
        return $min * 60 + $sec;
    }*/

    public function getTimingToSec() {
        $hours = $this->timing->format('H');
        $minutes = $this->timing->format('i');
        $secondes = $this->timing->format('s');
        return $hours * 3600 + $minutes * 60 + $secondes;
    }
}
