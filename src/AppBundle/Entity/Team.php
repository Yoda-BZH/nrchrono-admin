<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Team
 *
 * @ORM\Table(name="team", indexes={@ORM\Index(name="fk_team_1", columns={"id_race"}), @ORM\Index(name="fk_team_reference", columns={"id_reference"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TeamRepository")
 */
class Team
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
     * @ORM\Column(name="name", type="text", nullable=true)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="nb_heure_pause", type="integer", nullable=true)
     */
    private $nbHeurePause;

    /**
     * @var integer
     *
     * @ORM\Column(name="nb_person", type="integer", nullable=true)
     */
    private $nbPerson;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_reference", type="integer", nullable=true)
     */
    private $idReference;

    /**
     * @var string
     *
     * @ORM\Column(name="color", type="string", length=10, nullable=true)
     */
    private $color;

    /**
     * @var \Race
     *
     * @ORM\ManyToOne(targetEntity="Race", inversedBy="teams")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_race", referencedColumnName="id")
     * })
     */
    private $idRace;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Racer", mappedBy="idTeam")
     */
    private $racers;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Ranking", mappedBy="idTeam")
     */
    private $rankings;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Pause", mappedBy="idTeam")
     */
    private $pauses;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Prediction", mappedBy="idTeam")
     */
    private $predictions;

    /**
     * description
     *
     * @param void
     * @return void
     */
    public function __construct()
    {
        $this->racers = new ArrayCollection();
        $this->rankins = new ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return Team
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set nbHeurePause
     *
     * @param integer $nbHeurePause
     * @return Team
     */
    public function setNbHeurePause($nbHeurePause)
    {
        $this->nbHeurePause = $nbHeurePause;

        return $this;
    }

    /**
     * Get nbHeurePause
     *
     * @return integer
     */
    public function getNbHeurePause()
    {
        return $this->nbHeurePause;
    }

    /**
     * Set nbPerson
     *
     * @param integer $nbPerson
     * @return Team
     */
    public function setNbPerson($nbPerson)
    {
        $this->nbPerson = $nbPerson;

        return $this;
    }

    /**
     * Get nbPerson
     *
     * @return integer
     */
    public function getNbPerson()
    {
        return $this->nbPerson;
    }

    /**
     * Set idReference
     *
     * @param integer $idReference
     * @return Team
     */
    public function setIdReference($idReference)
    {
        $this->idReference = $idReference;

        return $this;
    }

    /**
     * Get idReference
     *
     * @return integer
     */
    public function getIdReference()
    {
        return $this->idReference;
    }

    /**
     * Set color
     *
     * @param string $color
     * @return Team
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color
     *
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Set idRace
     *
     * @param \AppBundle\Entity\Race $idRace
     * @return Team
     */
    public function setIdRace(\AppBundle\Entity\Race $idRace = null)
    {
        $this->idRace = $idRace;

        return $this;
    }

    /**
     * Get idRace
     *
     * @return \AppBundle\Entity\Race
     */
    public function getIdRace()
    {
        return $this->idRace;
    }

    /**
     * Return racers
     *
     * @return Collection
     */
    public function getRacers() {
        return $this->racers;
    }

    /**
     * description
     *
     * @return string
     */
    public function __toString() {
        return $this->getName();
    }

}
