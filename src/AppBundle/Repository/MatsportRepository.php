<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class MatsportRepository extends EntityRepository
{
    /**
     * description
     *
     * @param void
     * @return void
     *
     * used: src/AppBundle/Command/FakerRacerTimingCommand.php
     *       src/AppBundle/Command/FakerMatsportGenCommand.php
     */
    public function findLatestForTeam($teamId)
    {
        $qb = $this->createQueryBuilder('m');

        $qb
            ->where('m.idTeam = :id')
            ->setParameter('id', $teamId)
            ->orderBy('m.id', 'DESC')
            ->setMaxResults(1)
            ;

        return $qb->getQuery()->getSingleResult();
    }

    /**
     * used: src/AppBundle/Command/FakerMatsportGenCommand.php
     */
    public function nbLapForTeam($teamId)
    {
        $qb = $this->createQueryBuilder('m');

        $qb
            ->select('COUNT(m.id)')
            ->where('m.idTeam = :id')
            ->setParameter('id', $teamId)
            ;

        return $qb
            ->getQuery()
            ->getSingleResult()
            ;
    }


    /**
     * used: src/AppBundle/Controller/EmulationController.php
     */
    public function getTeamStats($team) {
        $qb = $this->createQueryBuilder('ms');

        $qb
            ->where('ms.idTeam = :team')
            ->setParameter('team', $team)
            //->andWhere('ti.createdAt > :yesterday')
            //->setParameter('yesterday', date('Y-m-d', time() - 3600 * 24))
            ->orderBy('ms.id', 'ASC')
            ;

        return $qb
            ->getQuery()
            ->getResult()
            ;
    }
}
