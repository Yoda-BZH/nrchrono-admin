<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

use AppBundle\Entity\Team;

class RacerRepository extends EntityRepository
{
    public function getAll()
    {
        return $this->createQueryBuilder('r')
            ->getQuery()
            ->getResult()
            ;
    }

    public function getAllByTeam($team)
    {
        return $this->createQueryBuilder('r')
            ->where('r.idTeam = :idTeam')
            ->setParameter('idTeam', $team)
            ->orderBy('r.position', 'ASC')
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
    public function findAllWithTeam()
    {
        $qb = $this->createQueryBuilder('r');
        $qb
            ->addSelect('t')
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
    public function getNextRacerAvailableQuery(Team $team, $position)
    {
        $qb = $this->createQueryBuilder('r');
        $qb
            ->addSelect('(CASE WHEN (r.position <= :position) THEN r.position + :nbPerson ELSE r.position END) AS HIDDEN nextPositions')
            ->where('r.idTeam = :idTeam')
            ->orderBy('nextPositions', 'asc')
            ->setParameter('position', $position)
            ->setParameter('nbPerson', $team->getNbPerson())
            ->setParameter('idTeam', $team)

            ->addSelect('rp')
            ->leftJoin('r.pauses', 'rp')
            ->addSelect('p')
            ->leftJoin('rp.idPause', 'p')
            ;

        return $qb;
    }

    public function getNextRacerAvailable(Team $team, $position) {
        $qb = $this->getNextRacerAvailableQuery($team, $position)
            ->setMaxResults(1)
            ;
        //var_dump($qb->getQuery()->getSQL(), $position, $team->getNbPerson(), $team->getId());

        return $qb->getQuery()->getSingleResult();
    }

    public function getAllRacersAvailable(Team $team, $position) {
        $qb = $this->getNextRacerAvailableQuery($team, $position)
            ;
        //var_dump($qb->getQuery()->getSQL(), $position, $team->getNbPerson(), $team->getId());

        return $qb->getQuery()->getResult();
    }

    public function getNextRacersAvailable(Team $team, $position) {
        return $this->getNextRacerAvailableQuery($team, $position)
            ->andWhere('r.position > :position')
            ->getQuery()
            ->getResult()
            ;
    }

    public function getFirstOfTeam($team) {
        $qb = $this->createQueryBuilder('r');

        $qb
            ->where('r.idTeam = :team')
            ->andWhere('r.position = :position')
            ->setParameter('team', $team)
            ->setParameter('position', 1)
            ;

        $racer = $qb->getQuery()->getSingleResult();

        return $racer;

    }

    public function getSecondOfTeam($team) {
        $qb = $this->createQueryBuilder('r');

        $qb
            ->where('r.idTeam = :team')
            ->andWhere('r.position = :position')
            ->setParameter('team', $team)
            ->setParameter('position', 2)
            ;

        $racer = $qb->getQuery()->getSingleResult();

        return $racer;

    }
}
