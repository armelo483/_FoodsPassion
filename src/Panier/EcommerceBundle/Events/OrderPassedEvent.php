<?php


namespace Panier\EcommerceBundle\Events;


use Panier\EcommerceBundle\Entity\Commande;
use Symfony\Component\EventDispatcher\Event;

class OrderPassedEvent extends Event
{
    const NAME = 'order.passed';

    protected $myOrderPassed;

    public function __construct(Commande $order)
    {
        $this->myOrderPlaced = $order;
    }

    public function getMyOrderPlaced()
    {
        return $this->myOrderPlaced;
    }
}