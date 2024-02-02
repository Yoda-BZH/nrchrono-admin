<?php

namespace App\Entity;

use App\Repository\PauseRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;


#[ORM\Entity(repositoryClass: PauseRepository::class)]
#[ORM\Index(name: "fk_pause_1_idx", columns: ["id_team"])]
#[ORM\Index(name: "ind_index_1", columns: ["porder"])]
class Pause
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var integer
     *
     * @ORM\Column(name="porder", type="integer", nullable=true)
     */
    #[ORM\Column]
    private ?int $porder = null;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="hour_start", type="datetime", nullable=true)
     */
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $hourStart = null;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="hour_stop", type="datetime", nullable=true)
     */
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $hourStop = null;

    #[ORM\OneToMany(targetEntity: RacerPause::class, mappedBy: 'pause')]
    private Collection $racerpauses;

    /**
     * @var \Team
     *
     * @ORM\ManyToOne(targetEntity="Team", inversedBy="pauses")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_team", referencedColumnName="id")
     * })
     */
    #[ORM\ManyToOne(targetEntity: Team::class, inversedBy: "pauses")]
    #[ORM\JoinColumn(name: "id_team", referencedColumnName: "id")]
    private Team|null $team = null;

    public function __construct()
    {
        $this->racerpauses = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set porder
     *
     * @param integer $porder
     * @return Pause
     */
    public function setPorder($porder)
    {
        $this->porder = $porder;

        return $this;
    }

    /**
     * Get porder
     *
     * @return integer
     */
    public function getPorder()
    {
        return $this->porder;
    }

    /**
     * Set hourStart
     *
     * @param \DateTime $hourStart
     * @return Pause
     */
    public function setHourStart($hourStart)
    {
        $this->hourStart = $hourStart;

        return $this;
    }

    /**
     * Get hourStart
     *
     * @return \DateTime
     */
    public function getHourStart()
    {
        return $this->hourStart;
    }

    /**
     * Set hourStop
     *
     * @param \DateTime $hourStop
     * @return Pause
     */
    public function setHourStop($hourStop)
    {
        $this->hourStop = $hourStop;

        return $this;
    }

    /**
     * Get hourStop
     *
     * @return \DateTime
     */
    public function getHourStop()
    {
        return $this->hourStop;
    }

    /**
     * Set idTeam
     *
     * @param \AppBundle\Entity\Team $idTeam
     * @return Pause
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
     * Return the value of
     *
     *
     */
    public function getName()
    {
        return sprintf('%s de %s à %s',
            $this->team->getName(),
            $this->hourStart->format('H:i'),
            $this->hourStop->format('H:i')
        );
    }

    public function getRacerPauses(): ?Collection
    {
        return $this->racerpauses;
    }

}
