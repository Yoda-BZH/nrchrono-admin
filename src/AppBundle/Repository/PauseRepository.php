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
     *
     * used: src/AppBundle/Controller/PauseController.php
     *       src/AppBundle/Controller/RacerController.php
     */
    public function findAllWithTeam()
    {
        $qb = $this->createQueryBuilder('p');
        $qb
            ->addSelect('t')
            ->leftJoin('p.idTeam', 'te')
            ;

        return $qb->getQuery()->getResult();
    }
}
