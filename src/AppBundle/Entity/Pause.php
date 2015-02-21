<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Pause
 *
 * @ORM\Table(name="pause", indexes={@ORM\Index(name="fk_pause_1", columns={"id_team"})})
 * @ORM\Entity
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
     * @var boolean
     *
     * @ORM\Column(name="index", type="boolean", nullable=true)
     */
    private $index;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="hour_start", type="time", nullable=true)
     */
    private $hourStart;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="hour_stop", type="time", nullable=true)
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


}
