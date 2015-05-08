<?php

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class TeamRepository extends EntityRepository
{
    /*
    public function getTeamWithRacers($teamId) {
        $qb = $this->createQueryBuilder('t');

        $qb
            ->addSelect('r')
            ->leftJoin('t.racers', 't')
            ->where('t.id = :id')
            ->setParameter('id', $teamId)
            ;

        return $qb
            ->getQuery()
            ->getSingleResult()
            ;
    }
    */


    public function getAll()
    {
        return $this->createQueryBuilder('t')
            ->getQuery()
            ->getResult()
            ;
    }


    public function getAllWithRacers()
    {
        $qb = $this->createQueryBuilder('t');

        $qb
            ->addSelect('r')
            ->leftJoin('t.racers', 'r')
            ;

        return $qb
            ->getQuery()
            ->getResult()
            ;
    }
}
