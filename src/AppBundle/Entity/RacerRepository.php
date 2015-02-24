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
            //->addSelect('(((position - 1 + :position) % 10) + 1) as nextPosition')
            ->addSelect('(MOD((r.position - 1 + :position), :nbPerson) + 1) AS HIDDEN nextPositions')
            ->where('r.idTeam = :idTeam')
            ->orderBy('nextPositions', 'asc')
            ->setParameter('idTeam', $team)
            ->setParameter('position', $position)
            ->setParameter('nbPerson', $team->getNbPerson())
            ->setMaxResults(1)
            ;
        var_dump($qb->getQuery()->getSQL());

        return $qb->getQuery()->getSingleResult();
    }
}


        /*$qb = $this->createQueryBuilder('r');

        $qb
            ->select('r, field(r.position, '.implode(', ', $nextPositions).') AS HIDDEN spositions')
            ->leftJoin('r.idTeam', 't')
            ->where('r.idTeam = :idTeam')
            ->orderBy('spositions')
            ->setMaxResults(1)
            ->setParameter('idTeam', $team)
            ;
        $qb = $this->createQueryBuilder('r');*/

        //~ $q = '
//~ SELECT
    //~ r,
    //~ FIELD(r.position, '.implode(', ', $nextPositions).') AS HIDDEN spositions
//~ FROM AppBundle\Entity\Racer r
//~ LEFT JOIN r.idTeam t
//~ WHERE r.idTeam = :idTeam
//~ ORDER BY spositions ASC
//~ ';
        //~ $query = $this->getEntityManager()->createQuery($q)
            //~ ->setParameter('idTeam', $team)
            //~ ;
//~
        //~ return $query->getResult();
