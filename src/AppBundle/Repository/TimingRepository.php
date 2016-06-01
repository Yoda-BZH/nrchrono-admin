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

    public function getStats(Racer $racer)
    {
        $qb = $this->createQueryBuilder('ti');
        $qb
            ->select($qb->expr()->min('ti.timing'))
            ->addSelect($qb->expr()->max('ti.timing'))
            //->addSelect($qb->expr()->avg('ti.timing'))
            ->addSelect('AVG(TIME_TO_SEC(ti.timing))')
            //->addSelect('AVG(UNIX_TIMESTAMP(ti.timing))')
            ->where('ti.idRacer = :idRacer')
            ->setParameter('idRacer', $racer)
            ;

        return $qb->getQuery()->getSingleResult();
    }

    public function getLatests() {
        $qb = $this->createQueryBuilder('ti');
        $qb
            //->orderBy('ti.id', 'DESC')
            ->orderBy('ti.createdAt', 'DESC')
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
        $qb = $this->createQueryBuilder('ti');
        $qb
            ->addSelect('r')
            ->addSelect('te')
            ->leftJoin('ti.idRacer', 'r')
            ->leftJoin('r.idTeam', 'te')
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
    public function getLatestRacerQuery($teamId)
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
            ->addOrderBy('ti.id', 'DESC')
            ;

        return $qb;
    }

    public function getLatestRacer($teamId)
    {
        return $this->getLatestTeamTiming($teamId)
            ->getIdRacer()
            ;
    }

    public function getLatestRacers($teamId) {
        return $this->getLatestRacerQuery($teamId)
            ->getQuery()
            ->getResult()
            ;
    }

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
            ->orderBy('ti.createdAt', 'ASC')
            ;

        return $qb
            ->getQuery()
            ->getResult()
            ;
    }

    public function getOlds($team, $id) {
        $qb = $this->createQueryBuilder('ti');
        $tree = 3;
        $qb
            ->leftJoin('ti.idRacer', 'r')
            ->where('ti.id < :id')
            ->setParameter('id', $id)
            ->andWhere('r.idTeam = :team')
            ->setParameter('team', $team)
            ->andWhere('ti.type = :type')
            ->setParameter('type', $tree)
            ;
        return $qb->getQuery()->getResult();
    }

    public function delOlds($team, $id) {
        $qb->update('AppBundle\Entity\Timing', 'ti')
            ->leftJoin('ti.idRacer', 'r')
            ->set('ti.type', ':type')
            ->setParameter('type', '6')
            ->where('ti.id < :id')
            ->setParameter('id', $id)
            ->andWhere('r.idTeam = :team')
            ->setParameter('team', $team)
            ;
        return $qb->getQuery()->getResult();
    }
    public function getNbForTeam($team)
    {
        $qb = $this->createQueryBuilder('ti');

        $qb
            ->select('COUNT(ti.id)')
            ->leftJoin('ti.idRacer', 'r')
            //->leftJoin('r.idTeam', 't'
            ->where('r.idTeam = :id')
            ->setParameter('id', $team)
            ;

        return $qb
            ->getQuery()
            ->getSingleResult()
            ;
    }

    public function getBestTeamLaps() {
        $qb = $this->createQueryBuilder('ti');

        $qb
            ->select($qb->expr()->min('ti.timing'))
            ->addSelect('te.name')
            ->addSelect('r.nickname')
            ->leftJoin('ti.idRacer', 'r')
            ->leftJoin('r.idTeam', 'te')
            //->addSelect('AVG(UNIX_TIMESTAMP(ti.timing))')
            ->groupBy('r.idTeam')
            ->orderBy('te.id', 'ASC')
            ;

        return $qb
            ->getQuery()
            ->getResult()
            ;
    }

    public function getLatestTeamLaps()
    {
        $qb = $this->createQueryBuilder('ti');

        $qb
            ->select('ti.timing')
            //->addSelect($qb->expr()->max('ti.id'))
            ->addSelect('ti.id timingid')
            ->addSelect('r.nickname')
            ->addSelect('ti.name')
            ->addSelect('ti.id teamid')
            ->leftJoin('ti.idRacer', 'r')
            ->leftJoin('r.idTeam', 't')
            ->addOrderBy('ti.createdAt', 'DESC')
            ->addOrderBy('r.idTeam', 'ASC')
            ->addOrderBy('ti.id', 'DESC')
            //->groupBy('r.idTeam')
            ->setMaxResults(5)
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
     */
    public function getNextPrediction($team)
    {
        $qb = $this->createQueryBuilder('ti');

        $qb
            ->addSelect('r')
            ->addSelect('te')
            ->leftJoin('ti.idRacer', 'r')
            ->leftJoin('r.idTeam', 'te')
            ->where('te.id = :idTeam')
            ->setParameter('idTeam', $team)
            ->andWhere('ti.type = :type')
            ->setParameter('type', Timing::PREDICTION)
            ->orderBy('ti.createdAt', 'ASC')
            ->setMaxResults(1)
            ;

        return $qb
            ->getQuery()
            ->getSingleResult()
            ;
    }
}
