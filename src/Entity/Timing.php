<?php

namespace App\Entity;

use App\Repository\TimingRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TimingRepository::class)]
#[ORM\Index(name: "fk_timing_1_idx",  columns: ["id_racer"])]
#[ORM\Index(name: "fk_timing_2_type", columns: ["type"])]
#[ORM\Index(name: "index_created_at", columns: ["created_at"])]
class Timing
{
    const AUTOMATIC  = 1;
    const MANUAL     = 2;
    const PREDICTION = 3;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $timing = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isRelay = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $clock = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $type = null;

    /**
     * @var \Racer
     *
     * @ORM\ManyToOne(targetEntity="Racer", inversedBy="timings")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_racer", referencedColumnName="id")
     * })
     */
    #[ORM\ManyToOne(targetEntity: Racer::class, inversedBy: "timings")]
    #[ORM\JoinColumn(name: "id_racer", referencedColumnName: "id")]
    private Racer|null $racer = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTiming(): ?\DateTimeInterface
    {
        return $this->timing;
    }

    public function setTiming(?\DateTimeInterface $timing): static
    {
        $this->timing = $timing;

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

    public function isIsRelay(): ?bool
    {
        return $this->isRelay;
    }

    public function setIsRelay(?bool $isRelay): static
    {
        $this->isRelay = $isRelay;

        return $this;
    }

    public function getClock(): ?\DateTimeInterface
    {
        return $this->clock;
    }

    public function setClock(?\DateTimeInterface $clock): static
    {
        $this->clock = $clock;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function getTypeStr(): ?string
    {
        $strings = array(
            self::AUTOMATIC => 'automatic',
            self::MANUAL => 'manual',
            self::PREDICTION => 'prediction',
        );

        return $strings[$this->type] ?: sprintf('Unknown prediction type (%d)', $this->type);
    }
    public function setType(int $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getRacer(): Racer|null
    {
        return $this->racer;
    }

    public function setRacer(Racer $racer): static
    {
        $this->racer = $racer;

        return $this;
    }



    public function setPrediction(): static
    {
        return $this->setType(self::PREDICTION);
    }

    public function isPrediction(): bool
    {
        return $this->type == self::PREDICTION;
    }

    public function setManual(): static
    {
        return $this->setType(self::MANUAL);
    }

    public function isManual(): bool
    {
        return $this->type == self::MANUAL;
    }

    public function setAutomatic(): static
    {
        return $this->setType(self::AUTOMATIC);
    }

    public function isAutomatic(): bool
    {
        return $this->type == self::AUTOMATIC;
    }

}
