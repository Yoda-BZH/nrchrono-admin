<?php

namespace AppBundle\Entity;


use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class TeamRepository extends EntityRepository
{
    public function getTeamWithRacers($team) {
        $qb = $this->createQueryBuilder('t');
        
        $qb
            ->addSelect('r')
            ->leftJoin('t.racers', 't')
            ;
    }
}
