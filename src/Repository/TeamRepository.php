<?php

namespace App\Repository;

use App\Entity\Team;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Team>
 *
 * @method Team|null find($id, $lockMode = null, $lockVersion = null)
 * @method Team|null findOneBy(array $criteria, array $orderBy = null)
 * @method Team[]    findAll()
 * @method Team[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TeamRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Team::class);
    }

//    /**
//     * @return Team[] Returns an array of Team objects
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

//    public function findOneBySomeField($value): ?Team
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    /**
     * description
     *
     * @param void
     * @return void
     */
    public function findAllWithGuest()
    {
        $qb = $this->createQueryBuilder('te');

        return $qb
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * used: many
     */
    public function getAll()
    {
        $qb = $this->createQueryBuilder('te');

        $qb
            ->andWhere('te.guest = :guest')
            ->setParameter('guest', false)
            ;

        return $qb
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * used: src/AppBundle/Controller/TimingController.php
     *       src/AppBundle/Controller/TimingFixController.php
     */
    public function getAllWithRacers(?int $team_id = 0)
    {
        $qb = $this->createQueryBuilder('te');

        // FIXME select only for the current race
        $qb
            ->addSelect('r')
            ->leftJoin('te.racers', 'r')
            ->where('te.guest = :guest')
            ->setParameter('guest', false)
            ;

        if ($team_id)
        {
            $qb
                ->andWhere('te.id = :teamid')
                ->setParameter('teamid', $team_id)
                ;
        }

        return $qb
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * used: src/AppBundle/Controller/TimingController.php
     */
    public function getWithRacersByPosition($id)
    {
        $qb = $this->createQueryBuilder('te');

        $qb
            ->addSelect('r')
            ->where('te.id = :id')
            ->leftJoin('te.racers', 'r')
            ->orderBy('r.position', 'ASC')
            ->setParameter('id', $id)
            ;

        return $qb
            ->getQuery()
            ->getSingleResult()
            ;
    }
}
