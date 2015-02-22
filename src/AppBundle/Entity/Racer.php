<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Racer
 *
 * @ORM\Table(name="racer", indexes={@ORM\Index(name="fk_racer_1_idx", columns={"id_team"})})
 * @ORM\Entity(repositoryClass="AppBundle\Entity\RacerRepository")
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
     * @var integer
     *
     * @ORM\Column(name="timing_min", type="integer", nullable=true)
     */
    private $timingMin;

    /**
     * @var integer
     *
     * @ORM\Column(name="timing_max", type="integer", nullable=true)
     */
    private $timingMax;

    /**
     * @var integer
     *
     * @ORM\Column(name="timing_avg", type="integer", nullable=true)
     */
    private $timingAvg;

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
     * Return the value of
     *
     *
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Return the value of
     *
     *
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set the value of
     *
     *
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Return the value of
     *
     *
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set the value of
     *
     *
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Return the value of
     *
     *
     */
    public function getNickname()
    {
        return $this->nickname;
    }

    /**
     * Set the value of
     *
     *
     */
    public function setNickname($nickname)
    {
        $this->nickname = $nickname;

        return $this;
    }

    /**
     * Return the value of
     *
     *
     */
    public function getTimingMax()
    {
        return $this->timingMax;
    }

    /**
     * Set the value of
     *
     *
     */
    public function setTimingMax($timingMax)
    {
        $this->timingMax = $timingMax;

        return $this;
    }

    /**
     * Return the value of
     *
     *
     */
    public function getTimingMin()
    {
        return $this->timingMin;
    }

    /**
     * Set the value of
     *
     *
     */
    public function setTimingMin($timingMin)
    {
        $this->timingMin = $timingMin;

        return $this;
    }

    /**
     * Return the value of
     *
     *
     */
    public function getTimingAvg()
    {
        return $this->timingAvg;
    }

    /**
     * Set the value of
     *
     *
     */
    public function setTimingAvg($timingAvg)
    {
        $this->timingAvg = $timingAvg;

        return $this;
    }

    /**
     * Return the value of
     *
     *
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set the value of
     *
     *
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Return the value of
     *
     *
     */
    public function getIdTeam()
    {
        return $this->idTeam;
    }

    /**
     * Set the value of
     *
     *
     */
    public function setIdTeam($idTeam)
    {
        $this->idTeam = $idTeam;

        return $this;
    }
}
