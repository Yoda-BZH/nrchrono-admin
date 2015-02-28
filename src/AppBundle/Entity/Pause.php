<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Pause
 *
 * @ORM\Table(name="pause", indexes={@ORM\Index(name="fk_pause_1_idx", columns={"id_team"}), @ORM\Index(name="ind_index_1", columns={"porder"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PauseRepository")
 */
class Pause
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
     * @var integer
     *
     * @ORM\Column(name="porder", type="integer", nullable=true)
     */
    private $porder;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="hour_start", type="datetime", nullable=true)
     */
    private $hourStart;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="hour_stop", type="datetime", nullable=true)
     */
    private $hourStop;

    /**
     * @var \Team
     *
     * @ORM\ManyToOne(targetEntity="Team")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_team", referencedColumnName="id")
     * })
     */
    private $idTeam;



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
    public function setIdTeam(\AppBundle\Entity\Team $idTeam = null)
    {
        $this->idTeam = $idTeam;

        return $this;
    }

    /**
     * Get idTeam
     *
     * @return \AppBundle\Entity\Team 
     */
    public function getIdTeam()
    {
        return $this->idTeam;
    }


    /**
     * Return the value of
     *
     *
     */
    public function getName()
    {
        return sprintf('%s de %s à %s',
            $this->idTeam->getName(),
            $this->hourStart->format('H:i'),
            $this->hourStop->format('H:i')
        );
    }

}
