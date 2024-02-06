<?php

namespace App\Repository;

use App\Entity\Timing;
use App\Entity\Team;
use App\Entity\Racer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Timing>
 *
 * @method Timing|null find($id, $lockMode = null, $lockVersion = null)
 * @method Timing|null findOneBy(array $criteria, array $orderBy = null)
 * @method Timing[]    findAll()
 * @method Timing[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TimingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Timing::class);
    }

//    /**
//     * @return Timing[] Returns an array of Timing objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Timing
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    // TimingController::indexAction
    public function findAllWithRacerTeam()
    {
        $qb = $this->createQueryBuilder('ti');
        $qb
            ->addSelect('r')
            ->addSelect('te')
            ->leftJoin('ti.racer', 'r')
            ->leftJoin('r.team', 'te')
            //->andWhere($qb->expr()->in('ti.type', array(Timing::MANUAL, Timing::PREDICTION)))
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
     *
     * used: src/AppBundle/Controller/TimingController.php x 2
     */
    public function findLatests($id)
    {
        $qb = $this->createQueryBuilder('ti');
        $qb
            ->addSelect('r')
            ->addSelect('te')
            ->leftJoin('ti.racer', 'r')
            ->leftJoin('r.team', 'te')
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
            ->where('ti.racer = :idRacer')
            ->setParameter('idRacer', $racer)
            ;

        $r = $qb->getQuery()->getSingleResult();
	$stats[1] = $r[1];

        $qb = $this->createQueryBuilder('ti');
        $qb
            ->select($qb->expr()->max('ti.timing'))
            //->addSelect($qb->expr()->avg('ti.timing'))
            //->addSelect('AVG(UNIX_TIMESTAMP(ti.timing))')
            ->where('ti.racer = :idRacer')
            ->setParameter('idRacer', $racer)
            ;

        $r = $qb->getQuery()->getSingleResult();
	$stats[2] = $r[1];

        $qb = $this->createQueryBuilder('ti');
        $qb
            ->select('AVG(TIME_TO_SEC(TIME(ti.timing)))')
            //->addSelect('AVG(UNIX_TIMESTAMP(ti.timing))')
            ->where('ti.racer = :idRacer')
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
     */
    protected function getLatestRacerQuery($teamId)
    {
        $qb = $this->createQueryBuilder('ti');
        $qb
            ->addSelect('r')
            ->leftJoin('ti.racer', 'r')
            ->where('r.team = :idTeam')
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
            ->getRacer()
            ;
    }

    /**
     * used: many
     */
    public function getLatestRacers($teamId)
    {
        return $this->getLatestRacerQuery($teamId)
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * used: many
     */
    public function getLatestTeamTiming($teamId, $limit = 1)
    {
        $qb = $this->getLatestRacerQuery($teamId)
            ->setMaxResults($limit)
            ;

        $query = $qb->getQuery();

        if(1 == $limit)
        {
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
            ->leftJoin('ti.racer', 'r')
            ->where('r.team = :idTeam')
            ->setParameter('idTeam', $team)
            ->orderBy('ti.createdAt', 'DESC')
            ->setMaxResults($team->getNbPerson() * 50)
            ;

        return $qb->getQuery()->getResult();
    }

    /**
     * used: src/AppBundle/Controller/StatRacerController.php
     */
    public function getRacerStats($racer)
    {
        $qb = $this->createQueryBuilder('ti');

        $qb
            ->where('ti.racer = :racer')
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
    public function getTeamStats($team)
    {
        $qb = $this->createQueryBuilder('ti');

        $qb
            ->leftJoin('ti.racer', 'r')
            ->where('r.team = :team')
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
            ->where('ti.racer = :racer')
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
            ->select('ti', 'r', 'rp')
            ->leftJoin('ti.racer', 'r')
            //->leftJoin('r.idTeam', 't')
            ->leftJoin('r.racerpauses', 'rp')
            ->where('r.team = :team')
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
            ->leftJoin('ti.racer', 'r')
            ->leftJoin('r.team', 't')
            ->where('ti.type = :type')
            ->andWhere('r.team = :idTeam')
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
            ->leftJoin('ti.racer', 'r')
            ->leftJoin('r.team', 't')
            ->where('ti.type = :type')
            ->andWhere('r.team = :team')
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

    public function getFutureOf(Timing $timing, Team $team)
    {
        $qb = $this->createQueryBuilder('ti');

        $qb
            ->select('ti', 'r', 't')
                ->leftJoin('ti.racer', 'r')
                    ->leftJoin('r.team', 't')
            ->andWhere($qb->expr()->in('ti.type', array(Timing::MANUAL, Timing::PREDICTION)))
            ->andWhere('t.id = :teamid')
                ->setParameter('teamid', $team->getId())
            ->andWhere('ti.id >= :timingid')
                ->setParameter('timingid', $timing->getId())
            ->addOrderBy('ti.id', 'ASC')
            ;

        return $qb
            ->getQuery()
            ->getResult()
            ;
    }
}
