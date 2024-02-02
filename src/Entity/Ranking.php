<?php

namespace App\Entity;

use App\Repository\RankingRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RankingRepository::class)]
#[ORM\Index(name: "fk_ranking_1_idx", columns: ["id_team"])]
#[ORM\Index(name: "fk_pos_1", columns: ["position"])]
class Ranking
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $position = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $time = null;

    #[ORM\Column(nullable: true)]
    private ?int $tour = null;

    #/**
    # * field "ecart" provided by a provider
    # * at matsport it can be either :
    # * * -
    # * * x Tr
    # * * 3:36.343
    # * * 38.321
    # * * 8:12:43.234
    # * ... so a string will do fine.
    # * @var string
    # *
    # * @ORM\Column(name="ecart", type="string", nullable=true, length=30)
    # */
    ##[ORM\Column(length: 30, nullable: true)]
    #private ?string $ecart = null;

    #[ORM\Column(nullable: true)]
    private ?int $distance = null;

    #[ORM\Column(nullable: true)]
    private ?int $speed = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $bestlap = null;

    #[ORM\Column(nullable: true)]
    private ?int $poscat = null;

    /**
     * @var \Team
     *
     * @ORM\ManyToOne(targetEntity="Team", inversedBy="rankings")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_team", referencedColumnName="id")
     * })
     */
    #[ORM\ManyToOne(targetEntity: Team::class, inversedBy: "rankings")]
    #[ORM\JoinColumn(name: "id_team", referencedColumnName: "id")]
    private Team|null $team = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getTime(): ?\DateTimeInterface
    {
        return $this->time;
    }

    public function setTime(?\DateTimeInterface $time): static
    {
        $this->time = $time;

        return $this;
    }

    public function getTour(): ?int
    {
        return $this->tour;
    }

    public function setTour(?int $tour): static
    {
        $this->tour = $tour;

        return $this;
    }

    #public function getEcart(): ?string
    #{
    #    return $this->ecart;
    #}
    #
    #public function setEcart(?string $ecart): static
    #{
    #    $this->ecart = $ecart;
    #
    #    return $this;
    #}

    public function getDistance(): ?int
    {
        return $this->distance;
    }

    public function setDistance(?int $distance): static
    {
        $this->distance = $distance;

        return $this;
    }

    public function getSpeed(): ?int
    {
        return $this->speed;
    }

    public function setSpeed(?int $speed): static
    {
        $this->speed = $speed;

        return $this;
    }

    public function getBestlap(): ?\DateTimeInterface
    {
        return $this->bestlap;
    }

    public function setBestlap(?\DateTimeInterface $bestlap): static
    {
        $this->bestlap = $bestlap;

        return $this;
    }

    public function getPoscat(): ?int
    {
        return $this->poscat;
    }

    public function setPoscat(?int $poscat): static
    {
        $this->poscat = $poscat;

        return $this;
    }

    public function setTeam(Team $team): static
    {
        $this->team = $team;

        return $this;
    }

    public function getTeam(): ?Team
    {
        return $this->team;
    }
}
