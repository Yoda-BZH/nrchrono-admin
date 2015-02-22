<?php

namespace AppBundle\Entity;


use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class TimingRepository extends EntityRepository
{
    public function getStats(Racer $racer)
    {
        $qb = $this->createQueryBuilder('t');
        $qb
            ->select($qb->expr()->min('t.timing'))
            ->addSelect($qb->expr()->max('t.timing'))
            ->addSelect($qb->expr()->avg('t.timing'))
            ->where('t.idRacer = :idRacer')
            ->setParameter('idRacer', $racer)
            ;

        return $qb->getQuery()->getSingleResult();
    }

    /**
     * description
     *
     * @param void
     * @return void
     */
    public function findAllWithRacer()
    {
        $qb = $this->createQueryBuilder('t');
        $qb
            ->addSelect('r')
            ->leftJoin('t.idRacer', 'r')
            ;

        return $qb->getQuery()->getResult();
    }
}
