<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Team
 *
 * @ORM\Table(name="team")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\TeamRepository")
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
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Racer", mappedBy="idTeam")
     */
    private $racers;

    /**
     * description
     *
     * @param void
     * @return void
     */
    public function __construct()
    {
        $this->racers = new ArrayCollection();
    }

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
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of
     *
     *
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Return the value of
     *
     *
     */
    public function getNbHeurePause()
    {
        return $this->nbHeurePause;
    }

    /**
     * Set the value of
     *
     *
     */
    public function setNbHeurePause($nbHeurePause)
    {
        $this->nbHeurePause = $nbHeurePause;

        return $this;
    }

    /**
     * Return the value of
     *
     *
     */
    public function getNbPerson()
    {
        return $this->nbPerson;
    }

    /**
     * Set the value of
     *
     *
     */
    public function setNbPerson($nbPerson)
    {
        $this->nbPerson = $nbPerson;

        return $this;
    }

    /**
     * Return the value of
     *
     *
     */
    public function getIdReference()
    {
        return $this->idReference;
    }

    /**
     * Set the value of
     *
     *
     */
    public function setIdReference($idReference)
    {
        $this->idReference = $idReference;

        return $this;
    }

    /**
     * description
     *
     * @param void
     * @return void
     */
    public function __toString()
    {
        return $this->getName();
    }

}
