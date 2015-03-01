<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ranking
 *
 * @ORM\Table(name="ranking", indexes={@ORM\Index(name="fk_ranking_1_idx", columns={"id_team"}), @ORM\Index(name="fk_pos_1", columns={"position"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RankingRepository")
 */
class Ranking
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
     * @ORM\Column(name="position", type="integer", nullable=true)
     */
    private $position;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time", type="time", nullable=true)
     */
    private $time;

    /**
     * @var integer
     *
     * @ORM\Column(name="tour", type="integer", nullable=true)
     */
    private $tour;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ecart", type="time", nullable=true)
     */
    private $ecart;

    /**
     * @var integer
     *
     * @ORM\Column(name="distance", type="integer", nullable=true)
     */
    private $distance;

    /**
     * @var integer
     *
     * @ORM\Column(name="speed", type="integer", nullable=true)
     */
    private $speed;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="bestlap", type="time", nullable=true)
     */
    private $bestlap;

    /**
     * @var integer
     *
     * @ORM\Column(name="poscat", type="integer", nullable=true)
     */
    private $poscat;

    /**
     * @var \Team
     *
     * @ORM\ManyToOne(targetEntity="Team", inversedBy="rankings")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_team", referencedColumnName="id")
     * })
     */
    private $idTeam;



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
     * Set position
     *
     * @param integer $position
     * @return Ranking
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Ranking
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
     * Set time
     *
     * @param \DateTime $time
     * @return Ranking
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Get time
     *
     * @return \DateTime 
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Set tour
     *
     * @param integer $tour
     * @return Ranking
     */
    public function setTour($tour)
    {
        $this->tour = $tour;

        return $this;
    }

    /**
     * Get tour
     *
     * @return integer 
     */
    public function getTour()
    {
        return $this->tour;
    }

    /**
     * Set ecart
     *
     * @param \DateTime $ecart
     * @return Ranking
     */
    public function setEcart($ecart)
    {
        $this->ecart = $ecart;

        return $this;
    }

    /**
     * Get ecart
     *
     * @return \DateTime 
     */
    public function getEcart()
    {
        return $this->ecart;
    }

    /**
     * Set distance
     *
     * @param integer $distance
     * @return Ranking
     */
    public function setDistance($distance)
    {
        $this->distance = $distance;

        return $this;
    }

    /**
     * Get distance
     *
     * @return integer 
     */
    public function getDistance()
    {
        return $this->distance;
    }

    /**
     * Set speed
     *
     * @param integer $speed
     * @return Ranking
     */
    public function setSpeed($speed)
    {
        $this->speed = $speed;

        return $this;
    }

    /**
     * Get speed
     *
     * @return integer 
     */
    public function getSpeed()
    {
        return $this->speed;
    }

    /**
     * Set bestlap
     *
     * @param \DateTime $bestlap
     * @return Ranking
     */
    public function setBestlap($bestlap)
    {
        $this->bestlap = $bestlap;

        return $this;
    }

    /**
     * Get bestlap
     *
     * @return \DateTime 
     */
    public function getBestlap()
    {
        return $this->bestlap;
    }

    /**
     * Set poscat
     *
     * @param integer $poscat
     * @return Ranking
     */
    public function setPoscat($poscat)
    {
        $this->poscat = $poscat;

        return $this;
    }

    /**
     * Get poscat
     *
     * @return integer 
     */
    public function getPoscat()
    {
        return $this->poscat;
    }

    /**
     * Set idTeam
     *
     * @param \AppBundle\Entity\Team $idTeam
     * @return Ranking
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
}
