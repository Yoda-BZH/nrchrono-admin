<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RacerPause
 *
 * @ORM\Table(name="racer_pause", indexes={@ORM\Index(name="fk_racer_pause_1_idx", columns={"id_racer"}), @ORM\Index(name="fk_racer_pause_2_idx", columns={"id_pause"})})
 * @ORM\Entity
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
     * @ORM\ManyToOne(targetEntity="Racer")
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


}
