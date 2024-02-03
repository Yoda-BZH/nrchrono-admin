<?php

namespace App\Repository;

use App\Entity\Racer;
use App\Entity\Team;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Racer>
 *
 * @method Racer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Racer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Racer[]    findAll()
 * @method Racer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RacerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Racer::class);
    }

//    /**
//     * @return Racer[] Returns an array of Racer objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Racer
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    /**
     * used: many
     */
    public function getAll()
    {
        return $this->createQueryBuilder('r')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * used: src/AppBundle/Controller/PredictionController.php
     */
    public function getAllByTeam($team = null, bool $guest = false)
    {
        $qb = $this->createQueryBuilder('r');

        $qb
            ->leftJoin('r.team', 'te')
            ->orderBy('r.position', 'ASC')
        ;
        if (!$guest)
        {
            $qb
                ->andWhere('te.guest = :guest')
                ->setParameter('guest', $guest)
            ;
        }

        if ($team)
        {
            $qb
                ->andWhere('r.team = :idTeam')
                ->setParameter('idTeam', $team)
                ;
        }

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
     * used: src/AppBundle/Controller/PauseController.php
     *       src/AppBundle/Controller/RacerController.php
     */
    public function findAllWithTeam()
    {
        $qb = $this->createQueryBuilder('r');
        $qb
            ->addSelect('te')
            ->leftJoin('r.team', 'te')
            ->andWhere('te.guest = :guest')
            ->setParameter('guest', false)
            ;

        return $qb->getQuery()->getResult();
    }

    /**
     * description
     *
     * @param void
     * @return void
     */
    protected function getNextRacerAvailableQuery(Team $team, $position, \DatetimeInterface $clock = null)
    {
        $qb = $this->createQueryBuilder('r');
        $qb
            ->addSelect('(CASE WHEN (r.position <= :position) THEN r.position + :nbPerson ELSE r.position END) AS HIDDEN nextPositions')
            ->where('r.team = :idTeam')
            ->orderBy('nextPositions', 'asc')
            ->setParameter('position', $position)
            ->setParameter('nbPerson', $team->getNbPerson())
            ->setParameter('idTeam', $team)
            ->andWhere('r.paused = :paused')
            ->setParameter('paused', false)

            ->addSelect('rp')
            ->leftJoin('r.racerpauses', 'rp')
            ->addSelect('p')
            ->leftJoin('rp.pause', 'p')
            ;
        //if ($clock)
        //{
        //    $qb
        //        ->andWhere($qb->expr()->not('p.hourStart <= :clock'))
        //        ->andWhere($qb->expr()->not('p.hourStop >= :clock'))
        //        ->setParameter('clock', $clock)
        //        ;
        //}

        return $qb;
    }

    /**
     * used: src/AppBundle/Service/NextRacerGuesser.php
     */
    public function getNextRacerAvailable(Team $team, $position, \DatetimeInterface $clock = null) {
        $qb = $this->getNextRacerAvailableQuery($team, $position, $clock)
            ->setMaxResults(1)
            ;
        //var_dump($qb->getQuery()->getSQL(), $position, $team->getNbPerson(), $team->getId());

        return $qb->getQuery()->getSingleResult();
    }

    /**
     * src/AppBundle/Service/NextRacerGuesser.php x 2
     */
    public function getAllRacersAvailable(Team $team, $position, \DatetimeInterface $clock = null) {
        $qb = $this->getNextRacerAvailableQuery($team, $position, $clock)
            ;
        //var_dump($qb->getQuery()->getSQL(), $position, $team->getNbPerson(), $team->getId());

        return $qb->getQuery()->getResult();
    }

    /**
     * used: src/AppBundle/Controller/PredictionController.php
     */
    public function getNextRacersAvailable(Team $team, $position, \DatetimeInterface $clock = null) {
        return $this->getNextRacerAvailableQuery($team, $position, $clock)
            ->andWhere('r.position > :position')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * used: src/AppBundle/Service/NextRacerGuesser.php
     */
    public function getFirstOfTeam($team) {
        $qb = $this->createQueryBuilder('r');

        $qb
            ->where('r.team = :team')
            ->andWhere('r.position = :position')
            ->setParameter('team', $team)
            ->setParameter('position', 1)
            ;

        $racer = $qb->getQuery()->getSingleResult();

        return $racer;

    }

    /**
     * used: src/AppBundle/Command/DashingRacerRunningCommand.php commented
     */
    //public function getSecondOfTeam($team) {
    //    $qb = $this->createQueryBuilder('r');
    //
    //    $qb
    //        ->where('r.idTeam = :team')
    //        ->andWhere('r.position = :position')
    //        ->setParameter('team', $team)
    //        ->setParameter('position', 2)
    //        ;
    //
    //    $racer = $qb->getQuery()->getSingleResult();
    //
    //    return $racer;
    //
    //}
}
