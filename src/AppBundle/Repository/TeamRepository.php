<?php

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class TeamRepository extends EntityRepository
{
    /**
     * used: many
     */
    public function getAll()
    {
        return $this->createQueryBuilder('te')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * used: src/AppBundle/Controller/TimingController.php
     *       src/AppBundle/Controller/TimingFixController.php
     */
    public function getAllWithRacers()
    {
        $qb = $this->createQueryBuilder('te');

        $qb
            ->addSelect('r')
            ->leftJoin('te.racers', 'r')
            ;

        return $qb
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * used: src/AppBundle/Controller/TimingController.php
     */
    public function getWithRacersByPosition($id)
    {
        $qb = $this->createQueryBuilder('te');

        $qb
            ->addSelect('r')
            ->where('te.id = :id')
            ->leftJoin('te.racers', 'r')
            ->orderBy('r.position', 'ASC')
            ->setParameter('id', $id)
            ;

        return $qb
            ->getQuery()
            ->getSingleResult()
            ;
    }
}
