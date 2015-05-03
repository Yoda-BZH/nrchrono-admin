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
}
