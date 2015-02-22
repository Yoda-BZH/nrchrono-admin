<?php

namespace AppBundle\Entity;


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
    /*public function getNextRacer($teamId)
    {
        $qbTi = $this->createQueryBuilder('ti');
        $qbTi
            ->from('Timing', 'ti')
            ->leftJoin('ti.idRacer', 'r')
            ->where('r.idTeam = :idTeam')
            ->setParameter('idTeam', $teamId)
            ;

        $resultTi = $qbTi->getQuery()->getResult();
        var_dump($resultTi);
        return;

        $qb = $this->createQueryBuilder('t');
        $qb
            ->leftJoin('t.racers', 'r')
            ;

    }*/
}
