<?php


namespace AppBundle\Listeners;


use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LoginListener
{

    protected $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {

      $this->session->set('onceAnimation', '1');

    }
}

