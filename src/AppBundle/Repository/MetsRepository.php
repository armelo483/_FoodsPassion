<?php


namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class MetsRepository extends EntityRepository
{
    public function findAllOrderedByLibelle()
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT p FROM AppBundle:Mets m ORDER BY m.libelle ASC'
            )
            ->getResult();
    }
}