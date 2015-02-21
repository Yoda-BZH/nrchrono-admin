<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Timing
 *
 * @ORM\Table(name="timing", indexes={@ORM\Index(name="fk_timing_1", columns={"id_racer"})})
 * @ORM\Entity
 */
class Timing
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
     * @ORM\Column(name="timing", type="integer", nullable=true)
     */
    private $timing;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_relay", type="boolean", nullable=true)
     */
    private $isRelay;

    /**
     * @var \Racer
     *
     * @ORM\ManyToOne(targetEntity="Racer")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_racer", referencedColumnName="id")
     * })
     */
    private $idRacer;


}
