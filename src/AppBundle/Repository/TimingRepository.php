<?php

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

use AppBundle\Entity\Racer;

class TimingRepository extends EntityRepository
{
    public function getStats(Racer $racer)
    {
        $qb = $this->createQueryBuilder('t');
        $qb
            ->select($qb->expr()->min('t.timing'))
            ->addSelect($qb->expr()->max('t.timing'))
            //->addSelect($qb->expr()->avg('t.timing'))
            ->addSelect('AVG(TIME_TO_SEC(t.timing))')
            //->addSelect('AVG(UNIX_TIMESTAMP(t.timing))')
            ->where('t.idRacer = :idRacer')
            ->setParameter('idRacer', $racer)
            ;

        return $qb->getQuery()->getSingleResult();
        /*$q = '
SELECT
    MIN(t.timing),
    MAX(t.timing),
    AVG(UNIX_TIMESTAMP(t.timing)
FROM AppBundle:Timing t
WHERE
    t.idRacer = :idRacer';
        $query = $this->getEntityManager()->createQuery($q)
            ->setParameter('idRacer', $racer)
            ;

        return $query->getResult();*/
    }

    public function getLatests() {
        $qb = $this->createQueryBuilder('t');
        $qb
            ->orderBy('t.id', 'DESC')
            ;

        $qb->getQuery()->getResult();
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
            ->orderBy('t.id', 'DESC')
            ->setMaxResults(200)
            ;

        return $qb->getQuery()->getResult();
    }

    /**
     * description
     *
     * @param void
     * @return void
     */
    public function getLatestRacerQuery($teamId)
    {
        $qb = $this->createQueryBuilder('ti');
        $qb
            //->addSelect($qb->expr()->max('ti.id'))
            ->addSelect('r')
            ->leftJoin('ti.idRacer', 'r')
            ->where('r.idTeam = :idTeam')
            ->setParameter('idTeam', $teamId)
            ->orderBy('ti.id', 'DESC')
            ;


        return $qb;
    }

    public function getLatestRacer($teamId)
    {
        $qb = $this->getLatestRacerQuery($teamId)
            ->setMaxResults(1)
            ;

        $r = $qb->getQuery()->getSingleResult();

        return $r->getIdRacer();
    }

    public function getLatestRacers($teamId) {
        return $this->getLatestRacerQuery($teamId)
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * description
     *
     * @param void
     * @return void
     */
    public function getRotations($team)
    {
        $qb = $this->createQueryBuilder('ti');
        $qb
            ->addSelect('r')
            ->leftJoin('ti.idRacer', 'r')
            ->where('r.idTeam = :idTeam')
            ->setParameter('idTeam', $team)
            ->orderBy('ti.id', 'DESC')
            ->setMaxResults($team->getNbPerson() * 50)
            ;

        return $qb->getQuery()->getResult();
    }

    public function getRacerStats($racer) {
        $qb = $this->createQueryBuilder('ti');

        $qb
            ->where('ti.idRacer = :racer')
            ->setParameter('racer', $racer)
            ;

        return $qb
            ->getQuery()
            ->getResult()
            ;
    }
}
