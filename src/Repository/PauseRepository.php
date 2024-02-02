<?php

namespace App\Repository;

use App\Entity\Pause;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Pause>
 *
 * @method Pause|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pause|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pause[]    findAll()
 * @method Pause[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PauseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pause::class);
    }

//    /**
//     * @return Pause[] Returns an array of Pause objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Pause
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function getAllWithTeamAndRacers()
    {
        $qb = $this->createQueryBuilder('p');
        $qb
            ->leftJoin('p.team', 't')
            ;

        return $qb
            ->getQuery()
            ->getResult()
            ;
    }

    public function getAllButNot(array $ids)
    {
        $qb = $this->createQueryBuilder('p');
        $qb
            ->leftJoin('p.team', 't')
            ;
        if($ids)
        {
            $qb
                ->andWhere($qb->expr()->notIn('p.id', $ids))
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
     * used: src/App/Controller/PauseController.php
     *       src/App/Controller/RacerController.php
     */
    public function findAllWithTeam()
    {
        $qb = $this->createQueryBuilder('p');
        $qb
            ->addSelect('te')
            ->leftJoin('p.team', 'te')
            ->andWhere('te.guest = :guest')
            ->setParameter('guest', false)
            ;

        return $qb->getQuery()->getResult();
    }

    public function getOneWithRacerPause($id)
    {
        $qb = $this->createQueryBuilder('p');
        $qb
            ->leftJoin('p.racerpauses', 'rp')
            ->leftJoin('rp.racer', 'r')
            //->leftJoin('p.team', 't')
            //->andWhere('t.guest = :guest')
            //->setParameter('guest', false)
            ->andWhere('p.id = :id')
            ->setParameter('id', $id);
        ;

        return $qb
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function getAllWithRacerPause()
    {
        $qb = $this->createQueryBuilder('p');
        $qb
            ->leftJoin('p.racerpauses', 'rp')
            ->leftJoin('rp.racer', 'r')
            ->leftJoin('p.team', 't')
            ->andWhere('t.guest = :guest')
            ->setParameter('guest', false)
        ;

        return $qb
            ->getQuery()
            ->getResult()
            ;
    }
}
