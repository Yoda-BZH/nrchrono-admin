<?php

namespace App\Repository;

use App\Entity\RacerPause;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RacerPause>
 *
 * @method RacerPause|null find($id, $lockMode = null, $lockVersion = null)
 * @method RacerPause|null findOneBy(array $criteria, array $orderBy = null)
 * @method RacerPause[]    findAll()
 * @method RacerPause[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RacerPauseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RacerPause::class);
    }

//    /**
//     * @return RacerPause[] Returns an array of RacerPause objects
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

//    public function findOneBySomeField($value): ?RacerPause
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
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
     *
     * used: src/AppBundle/Controller/RacerPauseController.php
     */
    public function findAllWithRacerTeamPause()
    {
        $qb = $this->createQueryBuilder('rp');
        $qb
            ->addSelect('r')
            ->leftJoin('rp.racer', 'r')
                ->addSelect('te')
                ->leftJoin('r.team', 'te')

            ->addSelect('p')
            ->leftJoin('rp.pause', 'p')
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
     *
     * used: src/AppBundle/Controller/PredictionController.php
     *       src/AppBundle/Controller/TeamPauseController.php
     */
    public function getTeamPauses($id)
    {
        $qb = $this->createQueryBuilder('rp');

        $qb
            ->addSelect('r')
            ->leftJoin('rp.racer', 'r')

            ->addSelect('p')
            ->leftJoin('rp.pause', 'p')

            ->addSelect('te')
            ->leftJoin('p.team', 'te')

            ->where('te.id = :id')
            ->setParameter('id', $id)

            ->orderBy('rp.porder', 'ASC')
            ->addOrderBy('p.hourStart', 'ASC')
            ;

        return $qb->getQuery()->getResult();
    }
}
