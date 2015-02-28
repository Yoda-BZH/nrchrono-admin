<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class PauseRepository extends EntityRepository
{

    /**
     * description
     *
     * @param void
     * @return void
     */
    public function findAllWithTeam()
    {
        $qb = $this->createQueryBuilder('p');
        $qb
            ->addSelect('t')
            ->leftJoin('p.idTeam', 't')
            ;

        return $qb->getQuery()->getResult();
    }
}
