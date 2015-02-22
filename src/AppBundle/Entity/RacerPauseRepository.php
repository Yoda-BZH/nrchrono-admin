<?php

namespace AppBundle\Entity;

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
                ->leftJoin('r.idTeam', 't')

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

            ->addSelect('t')
            ->leftJoin('p.idTeam', 't')

            ->where('t.id = :id')
            ->setParameter('id', $id)

            ->orderBy('rp.porder', 'ASC')
            ->addOrderBy('p.hourStart', 'ASC')
            ;

        return $qb->getQuery()->getResult();
    }
}
