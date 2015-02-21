<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Racer
 *
 * @ORM\Table(name="racer", indexes={@ORM\Index(name="fk_racer_1", columns={"id_team"})})
 * @ORM\Entity
 */
class Racer
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
     * @var string
     *
     * @ORM\Column(name="firstname", type="text", nullable=true)
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="text", nullable=true)
     */
    private $lastname;

    /**
     * @var string
     *
     * @ORM\Column(name="nickname", type="text", nullable=true)
     */
    private $nickname;

    /**
     * @var integer
     *
     * @ORM\Column(name="timing_min", type="integer", nullable=true)
     */
    private $timingMin;

    /**
     * @var integer
     *
     * @ORM\Column(name="timing_max", type="integer", nullable=true)
     */
    private $timingMax;

    /**
     * @var integer
     *
     * @ORM\Column(name="timing_avg", type="integer", nullable=true)
     */
    private $timingAvg;

    /**
     * @var integer
     *
     * @ORM\Column(name="position", type="integer", nullable=true)
     */
    private $position;

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
