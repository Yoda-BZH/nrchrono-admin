<?php

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

use AppBundle\Entity\Racer;
use AppBundle\Entity\Timing;
use AppBundle\Entity\Team;


class TimingRepository extends EntityRepository
{

    /**
     * description
     *
     * @param void
     * @return void
     *
     * used: src/AppBundle/Controller/TimingController.php x 2
     */
    public function findLatests($id)
    {
        $qb = $this->createQueryBuilder('ti');
        $qb
            ->addSelect('r')
            ->addSelect('te')
            ->leftJoin('ti.idRacer', 'r')
            ->leftJoin('r.idTeam', 'te')
            ->where('ti.id = :id')
            ->setParameter('id', $id)
            ;

        return $qb->getQuery()->getSingleResult();
    }

    /**
     * used: src/AppBundle/Command/RacerTimingConsolidateCommand.php
     */
    public function getStats(Racer $racer)
    {
        $stats = array();
        $qb = $this->createQueryBuilder('ti');
        $qb
            ->select($qb->expr()->min('ti.timing'))
            //->addSelect($qb->expr()->avg('ti.timing'))
            //->addSelect('AVG(UNIX_TIMESTAMP(ti.timing))')
            ->where('ti.idRacer = :idRacer')
            ->setParameter('idRacer', $racer)
            ;

        $r = $qb->getQuery()->getSingleResult();
	$stats[1] = $r[1];

        $qb = $this->createQueryBuilder('ti');
        $qb
            ->select($qb->expr()->max('ti.timing'))
            //->addSelect($qb->expr()->avg('ti.timing'))
            //->addSelect('AVG(UNIX_TIMESTAMP(ti.timing))')
            ->where('ti.idRacer = :idRacer')
            ->setParameter('idRacer', $racer)
            ;

        $r = $qb->getQuery()->getSingleResult();
	$stats[2] = $r[1];

        $qb = $this->createQueryBuilder('ti');
        $qb
            ->select('AVG(TIME_TO_SEC(TIME(ti.timing)))')
            //->addSelect('AVG(UNIX_TIMESTAMP(ti.timing))')
            ->where('ti.idRacer = :idRacer')
            ->setParameter('idRacer', $racer)
            ->andWhere($qb->expr()->in('ti.type', array(Timing::MANUAL)))
            ;

        $r = $qb->getQuery()->getSingleResult();
	$stats[3] = $r[1];
	return $stats;
    }

    //public function getLatests() {
    //    $qb = $this->createQueryBuilder('ti');
    //    $qb
    //        //->orderBy('ti.id', 'DESC')
    //        ->orderBy('ti.createdAt', 'DESC')
    //        ;
    //
    //    $qb->getQuery()->getResult();
    //}

    /**
     * description
     *
     * @param void
     * @return void
     *
     * used: src/AppBundle/Controller/TimingController.php
     *       src/AppBundle/Controller/RacerPauseController.php
     *       src/AppBundle/Repository/RacerPauseRepository.php
     */
    public function findAllWithRacerTeam()
    {
        $qb = $this->createQueryBuilder('ti');
        $qb
            ->addSelect('r')
            ->addSelect('te')
            ->leftJoin('ti.idRacer', 'r')
            ->leftJoin('r.idTeam', 'te')
            ->andWhere($qb->expr()->in('ti.type', array(Timing::MANUAL)))
            ->orderBy('ti.createdAt', 'DESC')
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
    protected function getLatestRacerQuery($teamId)
    {
        $qb = $this->createQueryBuilder('ti');
        $qb
            ->addSelect('r')
            ->leftJoin('ti.idRacer', 'r')
            ->where('r.idTeam = :idTeam')
            //->andWhere('ti.type = :type')
            //->andWhere($qb->expr()->in('ti.type', array(Timing::AUTOMATIC, Timing::MANUAL)))
            ->andWhere($qb->expr()->in('ti.type', array(Timing::MANUAL)))
            //->andWhere($qb->expr()->in('ti.type', array(Timing::AUTOMATIC)))
            ->setParameter('idTeam', $teamId)

            ->orderBy('ti.type', 'DESC')
            ->addOrderBy('ti.createdAt', 'DESC')
            ;

        return $qb;
    }


    /**
     * used: src/AppBundle/Controller/PredictionController.php
     *       src/AppBundle/Controller/TimingFixController.php
     */
    public function getLatestRacer($teamId)
    {
        return $this->getLatestTeamTiming($teamId)
            ->getIdRacer()
            ;
    }

    /**
     * used: many
     */
    public function getLatestRacers($teamId) {
        return $this->getLatestRacerQuery($teamId)
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * used: many
     */
    public function getLatestTeamTiming($teamId, $limit = 1) {
        $qb = $this->getLatestRacerQuery($teamId)
            ->setMaxResults($limit)
            ;

        $query = $qb->getQuery();

        if(1 == $limit) {
            return $query
                ->getSingleResult()
                ;
        }

        return $query->getResult();
    }

    /**
     * description
     *
     * @param void
     * @return void
     *
     * used: src/AppBundle/Controller/TeamController.php
     */
    public function getRotations($team)
    {
        $qb = $this->createQueryBuilder('ti');
        $qb
            ->addSelect('r')
            ->leftJoin('ti.idRacer', 'r')
            ->where('r.idTeam = :idTeam')
            ->setParameter('idTeam', $team)
            ->orderBy('ti.createdAt', 'DESC')
            ->setMaxResults($team->getNbPerson() * 50)
            ;

        return $qb->getQuery()->getResult();
    }

    /**
     * used: src/AppBundle/Controller/StatRacerController.php
     */
    public function getRacerStats($racer) {
        $qb = $this->createQueryBuilder('ti');

        $qb
            ->where('ti.idRacer = :racer')
            ->setParameter('racer', $racer)
            //->andWhere('ti.createdAt > :yesterday')
            //->setParameter('yesterday', date('Y-m-d', time() - 3600 * 24))
            ;

        return $qb
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * used: many
     */
    public function getTeamStats($team) {
        $qb = $this->createQueryBuilder('ti');

        $qb
            ->leftJoin('ti.idRacer', 'r')
            ->where('r.idTeam = :team')
            ->setParameter('team', $team)
            //->andWhere('ti.createdAt > :yesterday')
            //->setParameter('yesterday', date('Y-m-d', time() - 3600 * 24))
            ->andWhere('ti.type = :type')
            ->setParameter('type', Timing::MANUAL)
            ->orderBy('ti.createdAt', 'ASC')
            ;

        return $qb
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * description
     *
     * @param void
     * @return void
     *
     * used: many
     */
    public function getPrediction(Racer $racer)
    {
        $qb = $this->createQueryBuilder('ti');

        $qb
            ->where('ti.idRacer = :racer')
            ->andWhere('ti.type = :type')
            ->setParameter('racer', $racer)
            ->setParameter('type', Timing::PREDICTION)
            ->orderBy('ti.createdAt', 'DESC')
            ;

        return $qb
            ->getQuery()
            ->getSingleResult()
            ;
    }

    /**
     * description
     *
     * @param void
     * @return void
     *
     * used: src/AppBundle/Service/NextRacerGuesser.php
     */
    public function getPredictionsForTeam(Team $team)
    {
        $qb = $this->createQueryBuilder('ti');

        $qb
            ->select('ti', 'r')
            ->leftJoin('ti.idRacer', 'r')
            //->leftJoin('r.idTeam', 't')
            ->where('r.idTeam = :team')
            ->setParameter('team', $team)
            ->andWhere('ti.type = :type')
            ->setParameter('type', Timing::PREDICTION)
            ->orderBy('ti.clock', 'ASC')
            //->addOrderBy('ti.createdAt', 'ASC')
            ;

        return $qb
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * description
     *
     * @param void
     * @return void
     *
     * used: src/AppBundle/Repository/TimingRepository.php
     */
    public function getBestTeamLap($team)
    {
        $qb = $this->createQueryBuilder('ti');

        $qb
            ->leftJoin('ti.idRacer', 'r')
            ->leftJoin('r.idTeam', 't')
            ->where('ti.type = :type')
            ->andWhere('r.idTeam = :idTeam')
            ->setParameter('idTeam', $team)
            ->setParameter('type', Timing::MANUAL)
            ->orderBy('ti.timing', 'ASC')
            ->setMaxResults(1)
            ;

        return $qb
            ->getQuery()
            ->getSingleResult()
            ;
    }

    /**
     *
     * used: src/AppBundle/Command/DashingLatestlapsCommand.php
     */
    public function getLatestTeamLap($team)
    {
        $qb = $this->createQueryBuilder('ti');

        $qb
            ->select('ti.timing')
            //->addSelect($qb->expr()->max('ti.id'))
            ->addSelect('ti.id timingid')
            ->addSelect('r.nickname')
            ->addSelect('t.name')
            ->addSelect('ti.id teamid')
            ->leftJoin('ti.idRacer', 'r')
            ->leftJoin('r.idTeam', 't')
            ->where('ti.type = :type')
            ->andWhere('r.idTeam = :team')
            ->setParameter('type', Timing::MANUAL)
            ->setParameter('team', $team)
            ->addOrderBy('ti.clock', 'DESC')
            //->groupBy('r.idTeam')
            ->setMaxResults(1)
            ;

        return $qb
            ->getQuery()
            ->getSingleResult()
            ;
    }
}
