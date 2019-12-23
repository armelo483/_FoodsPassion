<?php


namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class CommentairesRepository extends EntityRepository
{
    public function findAllOrderedByLibelle()
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT p FROM AppBundle:Commentaires m ORDER BY m.notation DESC'
            )
            ->getResult();
    }
}