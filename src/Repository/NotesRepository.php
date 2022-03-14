<?php


namespace App\Repository;


use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;


/**
 * NotesRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class NotesRepository extends EntityRepository
{

    /**
     * @throws NonUniqueResultException
     */
    public function EquipeDejaNotee($jure_id, $equipe_id)
    {
        $queryBuilder = $this->createQueryBuilder('n');

        $queryBuilder
            ->where('n.jure=:jure_id')
            ->setParameter('jure_id', $jure_id)
            ->andwhere('n.equipe=:equipe_id')
            ->setParameter('equipe_id', $equipe_id);

        $query = $queryBuilder->getQuery();

        return $query->getOneOrNullResult();

    }

}