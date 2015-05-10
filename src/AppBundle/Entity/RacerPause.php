<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RacerPause
 *
 * @ORM\Table(name="racer_pause", indexes={@ORM\Index(name="fk_racer_pause_1_idx", columns={"id_racer"}), @ORM\Index(name="fk_racer_pause_2_idx", columns={"id_pause"}), @ORM\Index(name="fk_racer_pause_3_porder", columns={"porder"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RacerPauseRepository")
 */
class RacerPause
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
     * @var \Racer
     *
     * @ORM\ManyToOne(targetEntity="Racer", inversedBy="pauses")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_racer", referencedColumnName="id")
     * })
     */
    private $idRacer;

    /**
     * @var \Pause
     *
     * @ORM\ManyToOne(targetEntity="Pause")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_pause", referencedColumnName="id")
     * })
     */
    private $idPause;



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
     * @return RacerPause
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
     * Set idRacer
     *
     * @param \AppBundle\Entity\Racer $idRacer
     * @return RacerPause
     */
    public function setIdRacer(\AppBundle\Entity\Racer $idRacer = null)
    {
        $this->idRacer = $idRacer;

        return $this;
    }

    /**
     * Get idRacer
     *
     * @return \AppBundle\Entity\Racer
     */
    public function getIdRacer()
    {
        return $this->idRacer;
    }

    /**
     * Set idPause
     *
     * @param \AppBundle\Entity\Pause $idPause
     * @return RacerPause
     */
    public function setIdPause(\AppBundle\Entity\Pause $idPause = null)
    {
        $this->idPause = $idPause;

        return $this;
    }

    /**
     * Get idPause
     *
     * @return \AppBundle\Entity\Pause
     */
    public function getIdPause()
    {
        return $this->idPause;
    }
}
