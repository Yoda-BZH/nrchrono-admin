<?php

namespace App\Entity;

use App\Repository\RaceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;


#[ORM\Entity(repositoryClass: RaceRepository::class)]
class Race
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $start = null;

    #[ORM\Column(length: 10)]
    private ?string $km = null;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Team", mappedBy="idRace")
     */
    #[ORM\OneToMany(targetEntity: Team::class, mappedBy: "race")]
    private Collection $teams;

    public function __construct()
    {
        $this->teams = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(\DateTimeInterface $start): static
    {
        $this->start = $start;

        return $this;
    }

    public function getKm(): ?string
    {
        return $this->km;
    }

    public function setKm(string $km): static
    {
        $this->km = $km;

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }

    public function setTeams(Collection $teams): static
    {
        $this->teams = $teams;

        return $this;
    }

    public function getTeams(): ?Collection
    {
        return $this->teams;
    }

    public function addTeam(Team $team): static
    {
        $this->teams[] = $team;

        return $this;
    }

    public function isStarted(): bool
    {
        return $this->start->format('U') <= time();
    }
}
