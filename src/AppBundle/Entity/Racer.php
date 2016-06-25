<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Racer
 *
 * @ORM\Table(name="racer", indexes={@ORM\Index(name="fk_racer_1_idx", columns={"id_team"}), @ORM\Index(name="fk_racer_2_position", columns={"position"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RacerRepository")
 */
class Racer
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
     * @var string
     *
     * @ORM\Column(name="firstname", type="text", nullable=false)
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="text", nullable=false)
     */
    private $lastname;

    /**
     * @var string
     *
     * @ORM\Column(name="nickname", type="text", nullable=true)
     */
    private $nickname;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="timing_min", type="time", nullable=true)
     */
    private $timingMin;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="timing_max", type="time", nullable=true)
     */
    private $timingMax;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="timing_avg", type="time", nullable=true)
     */
    private $timingAvg;

    /**
     * @var paused
     *
     * @ORM\Column(name="paused", type="boolean", options={"default" = 0})
     */
    private $paused = false;

    /**
     * @var integer
     *
     * @ORM\Column(name="position", type="integer", nullable=true)
     */
    private $position;

    /**
     * @var \Team
     *
     * @ORM\ManyToOne(targetEntity="Team", inversedBy="racers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_team", referencedColumnName="id")
     * })
     */
    private $idTeam;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Timing", mappedBy="idRacer")
     */
    private $timings;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Prediction", mappedBy="idRacer")
     */
    private $predictions;

    /**
     * @var RacerPause
     *
     * @ORM\OneToMany(targetEntity="RacerPause", mappedBy="idRacer")
     */
    private $pauses;

    private $currentPrediction = null;

    /**
     * description
     *
     * @param void
     * @return void
     */
    public function __construct()
    {
        $this->timings = new ArrayCollection();
        $this->pauses = new ArrayCollection();
    }

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
     * Set firstname
     *
     * @param string $firstname
     * @return Racer
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     * @return Racer
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set nickname
     *
     * @param string $nickname
     * @return Racer
     */
    public function setNickname($nickname)
    {
        $this->nickname = $nickname;

        return $this;
    }

    /**
     * Get nickname
     *
     * @return string
     */
    public function getNickname()
    {
        return $this->nickname;
    }

    /**
     * Set timingMin
     *
     * @param \DateTime $timingMin
     * @return Racer
     */
    public function setTimingMin($timingMin)
    {
        $this->timingMin = $timingMin;

        return $this;
    }

    /**
     * Get timingMin
     *
     * @return \DateTime
     */
    public function getTimingMin()
    {
        return $this->timingMin;
    }

    /**
     * Set timingMax
     *
     * @param \DateTime $timingMax
     * @return Racer
     */
    public function setTimingMax($timingMax)
    {
        $this->timingMax = $timingMax;

        return $this;
    }

    /**
     * Get timingMax
     *
     * @return \DateTime
     */
    public function getTimingMax()
    {
        return $this->timingMax;
    }

    /**
     * Set timingAvg
     *
     * @param \DateTime $timingAvg
     * @return Racer
     */
    public function setTimingAvg($timingAvg)
    {
        $this->timingAvg = $timingAvg;

        return $this;
    }

    /**
     * Get timingAvg
     *
     * @return \DateTime
     */
    public function getTimingAvg()
    {
        return $this->timingAvg;
    }

    /**
     * Set position
     *
     * @param integer $position
     * @return Racer
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set idTeam
     *
     * @param \AppBundle\Entity\Team $idTeam
     * @return Racer
     */
    public function setIdTeam(\AppBundle\Entity\Team $idTeam = null)
    {
        $this->idTeam = $idTeam;

        return $this;
    }

    /**
     * Get idTeam
     *
     * @return \AppBundle\Entity\Team
     */
    public function getIdTeam()
    {
        return $this->idTeam;
    }

    public function getPauses()
    {
        return $this->pauses;
    }

    public function setPauses($pauses)
    {
        $this->pauses = $pauses;

        return $this;
    }

    /**
     * description
     *
     * @param void
     * @return void
     */
    public function getPaused()
    {
        return $this->paused;
    }

    /**
     * description
     *
     * @param void
     * @return void
     */
    public function setPaused($isPaused)
    {
        $this->paused = $isPaused;

        return $this;
    }
}
