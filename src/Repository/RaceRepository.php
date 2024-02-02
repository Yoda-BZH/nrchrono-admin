<?php

namespace App\Repository;

use App\Entity\Race;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Race>
 *
 * @method Race|null find($id, $lockMode = null, $lockVersion = null)
 * @method Race|null findOneBy(array $criteria, array $orderBy = null)
 * @method Race[]    findAll()
 * @method Race[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RaceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Race::class);
    }

//    /**
//     * @return Race[] Returns an array of Race objects
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

//    public function findOneBySomeField($value): ?Race
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function getCurrentRace(): ?Race
    {
        return $this->createQueryBuilder('r')
            //->andWhere('r.start < CURRENT_TIMESTAMP()')
            ->orderBy('r.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function getCurrentRaceWithTeams(bool $guest = true): ?Race
    {
        $qb = $this->createQueryBuilder('r');
        $qb
            //->andWhere('r.start < CURRENT_TIMESTAMP()')
            ->leftJoin('r.teams', 't')
            ->orderBy('r.id', 'DESC')
            ->setMaxResults(1)
            ;

        if (!$guest)
        {
            $qb
                ->andWhere('t.guest = :guest')
                ->setParameter('guest', $guest)
                ;
        }

        return $qb
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function getCurrentRaceWithTeamsAndRacers(bool $guest = true): ?Race
    {
        $qb = $this->createQueryBuilder('r');
        $qb
            //->andWhere('r.start < CURRENT_TIMESTAMP()')
            ->leftJoin('r.teams', 't')
            ->leftJoin('t.racers', 'ra')
            ->orderBy('r.id', 'DESC')
            ->setMaxResults(1)
            ;

        if (!$guest)
        {
            $qb
                ->andWhere('t.guest = :guest')
                ->setParameter('guest', $guest)
                ;
        }

        return $qb
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
