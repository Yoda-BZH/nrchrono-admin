<?php

namespace App\Entity;

use App\Repository\RacerPauseRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RacerPauseRepository::class)]
#[ORM\Index(name: "fk_racer_pause_1_idx", columns: ["id_racer"])]
#[ORM\Index(name: "fk_racer_pause_2_idx", columns: ["id_pause"])]
#[ORM\Index(name: "fk_racer_pause_3_porder", columns: ["porder"])]
class RacerPause
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $porder = null;

    /**
     * @var \Racer
     *
     * @ORM\ManyToOne(targetEntity="Racer", inversedBy="pauses")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_racer", referencedColumnName="id")
     * })
     */
    #[ORM\ManyToOne(targetEntity: Racer::class, inversedBy: "racerpauses")]
    #[ORM\JoinColumn(name: "id_racer", referencedColumnName: "id")]
    private Racer|null $racer = null;

    /**
     * @var \Pause
     *
     * @ORM\ManyToOne(targetEntity="Pause")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_pause", referencedColumnName="id")
     * })
     */
    #[ORM\ManyToOne(targetEntity: Pause::class, inversedBy: "racerpauses")]
    #[ORM\JoinColumn(name: "id_pause", referencedColumnName: "id")]
    private $pause;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPorder(): ?int
    {
        return $this->porder;
    }

    public function setPorder(?int $porder): static
    {
        $this->porder = $porder;

        return $this;
    }

    /**
     * Set idRacer
     *
     * @param \AppBundle\Entity\Racer $idRacer
     * @return RacerPause
     */
    public function setRacer(\App\Entity\Racer $racer = null)
    {
        $this->racer = $racer;

        return $this;
    }

    /**
     * Get idRacer
     *
     * @return \AppBundle\Entity\Racer
     */
    public function getRacer()
    {
        return $this->racer;
    }

    /**
     * Set idPause
     *
     * @param \AppBundle\Entity\Pause $idPause
     * @return RacerPause
     */
    public function setPause(\App\Entity\Pause $pause = null)
    {
        $this->pause = $pause;

        return $this;
    }

    /**
     * Get idPause
     *
     * @return \AppBundle\Entity\Pause
     */
    public function getPause()
    {
        return $this->pause;
    }
}
