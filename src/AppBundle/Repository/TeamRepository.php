<?php

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class TeamRepository extends EntityRepository
{
    /**
     * description
     *
     * @param void
     * @return void
     */
    public function findAll()
    {
        return $this->getAll();
    }

    /**
     * description
     *
     * @param void
     * @return void
     */
    public function findAllWithGuest()
    {
        $qb = $this->createQueryBuilder('te');

        return $qb
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * used: many
     */
    public function getAll()
    {
        $qb = $this->createQueryBuilder('te');

        $qb
            ->andWhere('te.guest = :guest')
            ->setParameter('guest', false)
            ;

        return $qb
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
            ->where('te.guest = :guest')
            ->setParameter('guest', false)
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
