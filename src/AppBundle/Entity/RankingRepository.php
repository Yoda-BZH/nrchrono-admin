<?php

namespace AppBundle\Entity;


use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class RankingRepository extends EntityRepository
{
    public function getLatestRankingForTeam($team)
    {
        $query = $this->createQueryBuilder('r')
            ->where('r.idTeam = :idTeam')
            ->orderBy('r.id', 'DESC')
            ->setMaxResults(1)
            ->setParameter('idTeam', $team->getId())
            ;
        try {
            return $query->getQuery()->getOneOrNullResult();
        } catch (NoResultException $e) {
            echo 'Cannot find last rank ...'.PHP_EOL;
            return null;
        }
    }
}
