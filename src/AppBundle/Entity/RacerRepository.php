<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class RacerRepository extends EntityRepository
{
    public function getAll()
    {
        return $this->createQueryBuilder('r')
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
            ->leftJoin('r.idTeam', 't')
            ;

        return $qb->getQuery()->getResult();
    }

    /**
     * description
     *
     * @param void
     * @return void
     */
    public function getNextRacerAvailable(Team $team, $position)
    {
        $qb = $this->createQueryBuilder('r');
        $qb
            ->addSelect('(CASE WHEN (r.position <= :position) THEN r.position + :nbPerson ELSE r.position END) AS HIDDEN nextPositions')
            ->where('r.idTeam = :idTeam')
            ->orderBy('nextPositions', 'asc')
            ->setParameter('position', $position)
            ->setParameter('nbPerson', $team->getNbPerson())
            ->setParameter('idTeam', $team)
            ->setMaxResults(1)
            ;
        //var_dump($qb->getQuery()->getSQL(), $position, $team->getNbPerson(), $team->getId());

        return $qb->getQuery()->getSingleResult();
    }
}
