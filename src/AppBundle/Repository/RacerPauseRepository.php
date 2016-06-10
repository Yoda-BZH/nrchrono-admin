<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class RacerPauseRepository extends EntityRepository
{

    /**
     * description
     *
     * @param void
     * @return void
     */
    public function findAllWithRacerTeamPause()
    {
        $qb = $this->createQueryBuilder('rp');
        $qb
            ->addSelect('r')
            ->leftJoin('rp.idRacer', 'r')
                ->addSelect('t')
                ->leftJoin('r.idTeam', 'te')

            ->addSelect('p')
            ->leftJoin('rp.idPause', 'p')

            ;

        return $qb->getQuery()->getResult();
    }

    /**
     * description
     *
     * @param void
     * @return void
     */
    public function getTeamPauses($id)
    {
        $qb = $this->createQueryBuilder('rp');

        $qb
            ->addSelect('r')
            ->leftJoin('rp.idRacer', 'r')

            ->addSelect('p')
            ->leftJoin('rp.idPause', 'p')

            ->addSelect('te')
            ->leftJoin('p.idTeam', 'te')

            ->where('te.id = :id')
            ->setParameter('id', $id)

            ->orderBy('rp.porder', 'ASC')
            ->addOrderBy('p.hourStart', 'ASC')
            ;

        return $qb->getQuery()->getResult();
    }
}
