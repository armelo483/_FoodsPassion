<?php

namespace Panier\EcommerceBundle\Events;

use Panier\EcommerceBundle\Entity\Commande;
use Symfony\Component\EventDispatcher\Event;

class CartDisplayedEvent extends Event
{
    const NAME = 'cart.displayed';

    protected $myOrderPlaced;

    public function __construct(Commande $order)
    {
        $this->myOrderPlaced = $order;
    }

    public function getMyOrderPlaced()
    {
        return $this->myOrderPlaced;
    }
}