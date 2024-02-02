<?php

namespace App\Entity;

use App\Repository\RacerRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Entity\Team;

#[ORM\Entity(repositoryClass: RacerRepository::class)]
#[ORM\Index(name: "fk_racer_1_idx", columns: ["id_team"])]
#[ORM\Index(name: "fk_racer_2_position", columns: ["position"])]
class Racer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $firstname = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $lastname = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $nickname = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $timingMin = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $timingMax = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $timingAvg = null;

    /**
     * @var integer
     *
     * @ORM\Column(name="position", type="integer", nullable=true)
     */
    #[ORM\Column]
    private ?int $position = null;

    #[ORM\Column(nullable: true)]
    private ?bool $paused = null;

    /**
     * @var \Team
     *
     * @ORM\ManyToOne(targetEntity="Team", inversedBy="racers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_team", referencedColumnName="id")
     * })
     */
    #[ORM\ManyToOne(targetEntity: Team::class, inversedBy: "racers")]
    #[ORM\JoinColumn(name: "id_team", referencedColumnName: "id")]
    private Team|null $team = null;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Timing", mappedBy="idRacer")
     */
    #[ORM\OneToMany(targetEntity: Timing::class, mappedBy: "racer")]
    private Collection $timings;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Prediction", mappedBy="idRacer")
     */
    #[ORM\OneToMany(targetEntity: Prediction::class, mappedBy: "racer")]
    private Collection $predictions;

    /**
     * @var RacerPause
     *
     * @ORM\OneToMany(targetEntity="RacerPause", mappedBy="idRacer")
     */
    #[ORM\OneToMany(targetEntity: RacerPause::class, mappedBy: "racer")]
    private Collection $racerpauses;

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
        $this->racerpauses = new ArrayCollection();
        $this->predictions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getNickname(): ?string
    {
        return $this->nickname ?: sprintf('%s %s', $this->getFirstname(), $this->getLastname());
    }

    public function setNickname(?string $nickname): static
    {
        $this->nickname = $nickname;

        return $this;
    }

    public function getTimingMin(): ?\DateTimeInterface
    {
        return $this->timingMin ?: new \Datetime('00:00:00');
    }

    public function setTimingMin(?\DateTimeInterface $timingMin): static
    {
        $this->timingMin = $timingMin;

        return $this;
    }

    public function getTimingMax(): ?\DateTimeInterface
    {
        return $this->timingMax ?: new \Datetime('00:00:00');
    }

    public function setTimingMax(?\DateTimeInterface $timingMax): static
    {
        $this->timingMax = $timingMax;

        return $this;
    }

    public function getTimingAvg(): ?\DateTimeInterface
    {
        return $this->timingAvg ?: new \Datetime('00:00:00');
    }

    public function setTimingAvg(?\DateTimeInterface $timingAvg): static
    {
        $this->timingAvg = $timingAvg;

        return $this;
    }

    public function isPaused(): ?bool
    {
        return $this->paused;
    }

    public function setPaused(?bool $paused): static
    {
        $this->paused = $paused;

        return $this;
    }

    /**
     * Set position
     *
     * @param integer $position
     * @return Racer
     */
    public function setPosition($position): static
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * Set idTeam
     *
     * @param \AppBundle\Entity\Team $idTeam
     * @return Racer
     */
    public function setTeam(Team $team = null): static
    {
        $this->team = $team;

        return $this;
    }

    /**
     * Get idTeam
     *
     * @return \AppBundle\Entity\Team
     */
    public function getTeam(): Team
    {
        return $this->team;
    }

    public function __toString(): String
    {
        return $this->nickname ?? sprintf('%s %s', $this->firstname, $this->lastname);
    }

    public function getRacerPauses(): Collection
    {
        return $this->racerpauses;
    }

}
