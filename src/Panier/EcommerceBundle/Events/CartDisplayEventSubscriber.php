<?php


namespace Panier\EcommerceBundle\Events;


use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CartDisplayEventSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return [
            CartDisplayedEvent::NAME => 'onCartDisplayed'
        ];
    }

    public function onCartDisplayed(Event $event){

    }
}