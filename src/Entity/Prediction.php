<?php

namespace App\Entity;

use App\Repository\PredictionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PredictionRepository::class)]
#[ORM\Index(name: "fk_prediction_1", columns: ["id_team"])]
#[ORM\Index(name: "fk_prediction_2", columns: ["id_racer"])]
class Prediction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var \Team
     *
     * @ORM\ManyToOne(targetEntity="Team", inversedBy="predictions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_team", referencedColumnName="id")
     * })
     */
    #[ORM\ManyToOne(targetEntity: Team::class, inversedBy: "predictions")]
    #[ORM\JoinColumn(name: "id_team", referencedColumnName: "id")]
    private Team|null $team = null;

    /**
     * @var \Racer
     *
     * @ORM\ManyToOne(targetEntity="Racer", inversedBy="predictions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_racer", referencedColumnName="id")
     * })
     */
    #[ORM\ManyToOne(targetEntity: Racer::class, inversedBy: "predictions")]
    #[ORM\JoinColumn(name: "id_racer", referencedColumnName: "id")]
    private Racer|null $racer = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set idTeam
     *
     * @param \AppBundle\Entity\Team $idTeam
     * @return Prediction
     */
    public function setTeam(\App\Entity\Team $team = null)
    {
        $this->team = $team;

        return $this;
    }

    /**
     * Get idTeam
     *
     * @return \AppBundle\Entity\Team
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * Set racer
     *
     * @param \AppBundle\Entity\Racer $racer
     * @return Prediction
     */
    public function setRacer(\App\Entity\Racer $racer = null)
    {
        $this->racer = $racer;

        return $this;
    }

    /**
     * Get racer
     *
     * @return \AppBundle\Entity\Racer
     */
    public function getRacer()
    {
        return $this->racer;
    }
}
