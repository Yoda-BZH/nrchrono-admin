<?php

namespace AppBundle\Entity;


use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class TimingRepository extends EntityRepository
{
    public function getStats(Racer $racer)
    {
        $qb = $this->createQueryBuilder('t');
        $qb
            ->select($qb->expr()->min('t.timing'))
            ->addSelect($qb->expr()->max('t.timing'))
            ->addSelect($qb->expr()->avg('t.timing'))
            ->where('t.idRacer = :idRacer')
            ->setParameter('idRacer', $racer)
            ;

        return $qb->getQuery()->getSingleResult();
    }

    /**
     * description
     *
     * @param void
     * @return void
     */
    public function findAllWithRacerTeam()
    {
        $qb = $this->createQueryBuilder('t');
        $qb
            ->addSelect('r')
            ->addSelect('te')
            ->leftJoin('t.idRacer', 'r')
            ->leftJoin('r.idTeam', 'te')
            ;

        return $qb->getQuery()->getResult();
    }

    /**
     * description
     *
     * @param void
     * @return void
     */
    public function getLatestRacer($teamId)
    {
        $qb = $this->createQueryBuilder('ti');
        $qb
            ->addSelect($qb->expr()->max('ti.id'))
            ->leftJoin('ti.idRacer', 'r')
            ->where('r.idTeam = :idTeam')
            ->setParameter('idTeam', $teamId)
            ;


        $r = $qb->getQuery()->getSingleResult();

        return $r[0]->getIdRacer();
    }
}
