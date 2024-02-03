<?php

namespace App\Entity;

use App\Repository\TeamRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;


#[ORM\Entity(repositoryClass: TeamRepository::class)]
#[ORM\Index(name: "fk_team_1", columns: ["id_race"])]
#[ORM\Index(name: "fk_team_reference", columns: ["id_reference"])]
class Team
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\Column]
    private ?string $name = null;

    /**
     * @var integer
     *
     * @ORM\Column(name="nb_heure_pause", type="integer", nullable=true)
     */
    #[ORM\Column(name: "nb_heure_pause")]
    private ?int $nbHeurePause = null;

    /**
     * @var integer
     *
     * @ORM\Column(name="nb_person", type="integer", nullable=true)
     */
    #[ORM\Column(name: "nb_person")]
    private ?int $nbPerson;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_reference", type="integer", nullable=true)
     */
    #[ORM\Column(name: "id_reference")]
    private ?int $idReference;

    /**
     * @var string
     *
     * @ORM\Column(name="color", type="string", length=10, nullable=true)
     */
    #[ORM\Column(length: 10)]
    private ?string $color = null;

    /**
     * @®ar boolean
     *
     * @ORM\Column(name="guest", type="boolean", options={"default"=0})
     */
    #[ORM\Column(type: "boolean")]
    private ?bool $guest = false;

    /**
     * @var \Race
     *
     * @ORM\ManyToOne(targetEntity="Race", inversedBy="teams")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_race", referencedColumnName="id")
     * })
     */
    #[ORM\ManyToOne(targetEntity: Race::class, inversedBy: "teams")]
    #[ORM\JoinColumn(name: "id_race", referencedColumnName: "id")]
    private Race|null $race = null;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Racer", mappedBy="idTeam")
     */
    #[ORM\OneToMany(targetEntity: Racer::class, mappedBy: "team")]
    private Collection $racers;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Ranking", mappedBy="idTeam")
     */
    #[ORM\OneToMany(targetEntity: Ranking::class, mappedBy: "team")]
    private Collection $rankings;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Pause", mappedBy="idTeam")
     */
    #[ORM\OneToMany(targetEntity: Pause::class, mappedBy: "team")]
    private Collection $pauses;


    /**
     * description
     *
     * @param void
     * @return void
     */
    public function __construct()
    {
        $this->racers = new ArrayCollection();
        $this->rankings = new ArrayCollection();
        $this->pauses = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Team
     */
    public function setName($name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set nbHeurePause
     *
     * @param integer $nbHeurePause
     * @return Team
     */
    public function setNbHeurePause($nbHeurePause): static
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
    public function setRace(\App\Entity\Race $race = null)
    {
        $this->race = $race;

        return $this;
    }

    /**
     * Get idRace
     *
     * @return \AppBundle\Entity\Race
     */
    public function getRace()
    {
        return $this->race;
    }

    /**
     * Return racers
     *
     * @return Collection
     */
    public function getRacers()
    {
        return $this->racers;
    }

    /**
     * description
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * Return the value of
     *
     *
     */
    public function getGuest()
    {
        return $this->guest;
    }

    /**
     * Set the value of
     *
     *
     */
    public function setGuest($guest)
    {
        $this->guest = $guest;

        return $this;
    }

    /**
     * description
     *
     * @param void
     * @return void
     */
    public function isGuest()
    {
        return $this->isGuest();
    }

}
