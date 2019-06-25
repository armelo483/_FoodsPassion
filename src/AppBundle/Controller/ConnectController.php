<?php

/*
 * This file is part of the HWIOAuthBundle package.
 *
 * (c) Hardware.Info <opensource@hardware.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Controller;

use HWI\Bundle\OAuthBundle\Event\FilterUserResponseEvent;
use HWI\Bundle\OAuthBundle\Event\FormEvent;
use HWI\Bundle\OAuthBundle\Event\GetResponseUserEvent;
use HWI\Bundle\OAuthBundle\HWIOAuthEvents;
use HWI\Bundle\OAuthBundle\OAuth\ResourceOwnerInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\Authentication\Token\OAuthToken;
use HWI\Bundle\OAuthBundle\Security\Core\Exception\AccountNotLinkedException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AccountStatusException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

/**
 * @author Alexander <iam.asm89@gmail.com>
 */
class ConnectController extends Controller
{

    /**
     * Shows a registration form if there is no user logged in and connecting
     * is enabled.
     *
     * @param Request $request a request
     * @param string  $key     key used for retrieving the right information for the registration form
     *
     * @return Response
     *
     * @throws NotFoundHttpException if `connect` functionality was not enabled
     * @throws AccessDeniedException if any user is authenticated
     * @throws \RuntimeException
     */
    public function registrationAction(Request $request, $key)
    {
        $connect = $this->container->getParameter('hwi_oauth.connect');
        if (!$connect) {
            throw new NotFoundHttpException();
        }

        $hasUser = $this->isGranted($this->container->getParameter('hwi_oauth.grant_rule'));
        if ($hasUser) {
            throw new AccessDeniedException('Cannot connect already registered account.');
        }

        $session = $request->getSession();
        if (!$session->isStarted()) {
            $session->start();
        }

        $error = $session->get('_hwi_oauth.registration_error.'.$key);
        $session->remove('_hwi_oauth.registration_error.'.$key);

        if (!$error instanceof AccountNotLinkedException) {
            //throw new \RuntimeException('Cannot register an account.', 0, $error instanceof \Exception ? $error : null);
            $this->addFlash('notice','cannot register an account');
            $this->redirectToRoute('homepage');
        }

        $userInformation = $this
            ->getResourceOwnerByName($error->getResourceOwnerName())
            ->getUserInformation($error->getRawToken())
        ;

        /* @var $form FormInterface */
        if ($this->container->getParameter('hwi_oauth.fosub_enabled')) {
            // enable compatibility with FOSUserBundle 1.3.x and 2.x
            if (interface_exists('FOS\UserBundle\Form\Factory\FactoryInterface')) {
                $form = $this->container->get('hwi_oauth.registration.form.factory')->createForm();
            } else {
                $form = $this->container->get('hwi_oauth.registration.form');
            }
        } else {
            $form = $this->container->get('hwi_oauth.registration.form');
        }

        $formHandler = $this->container->get('hwi_oauth.registration.form.handler');
        if ($formHandler->process($request, $form, $userInformation)) {
            $event = new FormEvent($form, $request);
            $this->get('event_dispatcher')->dispatch(HWIOAuthEvents::REGISTRATION_SUCCESS, $event);

            $this->container->get('hwi_oauth.account.connector')->connect($form->getData(), $userInformation);

            // Authenticate the user
            $this->authenticateUser($request, $form->getData(), $error->getResourceOwnerName(), $error->getAccessToken());

            if (null === $response = $event->getResponse()) {
                if ($targetPath = $this->getTargetPath($session)) {
                    $response = $this->redirect($targetPath);
                } else {
                    $response = $this->render('@HWIOAuth/Connect/registration_success.html.twig', array(
                        'userInformation' => $userInformation,
                    ));

                    $this->addFlash('success','Welcome amongst us  '.$userInformation->getRealName());

                    return  $this->redirectToRoute('homepage');

                }
            }

            $event = new FilterUserResponseEvent($form->getData(), $request, $response);
            $this->get('event_dispatcher')->dispatch(HWIOAuthEvents::REGISTRATION_COMPLETED, $event);

            return $response;
        }

        // reset the error in the session
        $session->set('_hwi_oauth.registration_error.'.$key, $error);

        $event = new GetResponseUserEvent($form->getData(), $request);
        $this->get('event_dispatcher')->dispatch(HWIOAuthEvents::REGISTRATION_INITIALIZE, $event);

        if ($response = $event->getResponse()) {
            return $response;
        }

        return $this->render('@HWIOAuth/Connect/registration.html.twig', array(
            'key' => $key,
            'form' => $form->createView(),
            'userInformation' => $userInformation,
        ));
    }


    /**
     * @param Request $request
     * @param string  $service
     *
     * @throws NotFoundHttpException
     *
     * @return RedirectResponse
     */
    public function redirectToServiceAction(Request $request, $service)
    {
        try {
            $authorizationUrl = $this->container->get('hwi_oauth.security.oauth_utils')->getAuthorizationUrl($request, $service);
        } catch (\RuntimeException $e) {
            throw new NotFoundHttpException($e->getMessage(), $e);
        }

        $session = $request->getSession();

        // Check for a return path and store it before redirect
        if (null !== $session) {
            // initialize the session for preventing SessionUnavailableException
            if (!$session->isStarted()) {
                $session->start();
            }

            foreach ($this->container->getParameter('hwi_oauth.firewall_names') as $providerKey) {
                $sessionKey = '_security.'.$providerKey.'.target_path';
                $sessionKeyFailure = '_security.'.$providerKey.'.failed_target_path';

                $param = $this->container->getParameter('hwi_oauth.target_path_parameter');
                if (!empty($param) && $targetUrl = $request->get($param)) {
                    $session->set($sessionKey, $targetUrl);
                }

                if ($this->container->getParameter('hwi_oauth.failed_use_referer') && !$session->has($sessionKeyFailure) && ($targetUrl = $request->headers->get('Referer')) && $targetUrl !== $authorizationUrl) {
                    $session->set($sessionKeyFailure, $targetUrl);
                }

                if ($this->container->getParameter('hwi_oauth.use_referer') && !$session->has($sessionKey) && ($targetUrl = $request->headers->get('Referer')) && $targetUrl !== $authorizationUrl) {
                    $session->set($sessionKey, $targetUrl);
                }
            }
        }

        return $this->redirect($authorizationUrl);
    }

    /**
     * Get the security error for a given request.
     *
     * @param Request $request
     *
     * @return string|\Exception
     */
    protected function getErrorForRequest(Request $request)
    {
        $authenticationErrorKey = Security::AUTHENTICATION_ERROR;

        if ($request->attributes->has($authenticationErrorKey)) {
            return $request->attributes->get($authenticationErrorKey);
        }

        $session = $request->getSession();
        if (null !== $session && $session->has($authenticationErrorKey)) {
            $error = $session->get($authenticationErrorKey);
            $session->remove($authenticationErrorKey);

            return $error;
        }

        return '';
    }

    /**
     * Get a resource owner by name.
     *
     * @param string $name
     *
     * @return ResourceOwnerInterface
     *
     * @throws NotFoundHttpException if there is no resource owner with the given name
     */
    protected function getResourceOwnerByName($name)
    {
        foreach ($this->container->getParameter('hwi_oauth.firewall_names') as $firewall) {
            $id = 'hwi_oauth.resource_ownermap.'.$firewall;
            if (!$this->container->has($id)) {
                continue;
            }

            $ownerMap = $this->container->get($id);
            if ($resourceOwner = $ownerMap->getResourceOwnerByName($name)) {
                return $resourceOwner;
            }
        }

        throw new NotFoundHttpException(sprintf("No resource owner with name '%s'.", $name));
    }

    /**
     * Generates a route.
     *
     * @deprecated since version 0.4. Will be removed in 1.0.
     *
     * @param string $route    Route name
     * @param array  $params   Route parameters
     * @param bool   $absolute absolute url or note
     *
     * @return string
     */
    protected function generate($route, array $params = array(), $absolute = false)
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 0.4 and will be removed in 1.0. Use Symfony\Bundle\FrameworkBundle\Controller\Controller::generateUrl instead.', E_USER_DEPRECATED);

        return $this->container->get('router')->generate($route, $params, $absolute);
    }

    /**
     * Authenticate a user with Symfony Security.
     *
     * @param Request       $request
     * @param UserInterface $user
     * @param string        $resourceOwnerName
     * @param string        $accessToken
     * @param bool          $fakeLogin
     */
    protected function authenticateUser(Request $request, UserInterface $user, $resourceOwnerName, $accessToken, $fakeLogin = true)
    {
        try {
            $this->container->get('hwi_oauth.user_checker')->checkPreAuth($user);
            $this->container->get('hwi_oauth.user_checker')->checkPostAuth($user);
        } catch (AccountStatusException $e) {
            // Don't authenticate locked, disabled or expired users
            return;
        }

        $token = new OAuthToken($accessToken, $user->getRoles());
        $token->setResourceOwnerName($resourceOwnerName);
        $token->setUser($user);
        $token->setAuthenticated(true);

        $this->get('security.token_storage')->setToken($token);

        if ($fakeLogin) {
            // Since we're "faking" normal login, we need to throw our INTERACTIVE_LOGIN event manually
            $this->container->get('event_dispatcher')->dispatch(
                SecurityEvents::INTERACTIVE_LOGIN,
                new InteractiveLoginEvent($request, $token)
            );
        }
    }

    /**
     * @param SessionInterface $session
     *
     * @return string|null
     */
    private function getTargetPath(SessionInterface $session)
    {
        foreach ($this->container->getParameter('hwi_oauth.firewall_names') as $providerKey) {
            $sessionKey = '_security.'.$providerKey.'.target_path';
            if ($session->has($sessionKey)) {
                return $session->get($sessionKey);
            }
        }

        return null;
    }

    /**
     * @param Request $request     The active request
     * @param array   $accessToken The access token
     * @param string  $service     Name of the resource owner to connect to
     *
     * @return Response
     *
     * @throws NotFoundHttpException if there is no resource owner with the given name
     */
    private function getConfirmationResponse(Request $request, array $accessToken, $service)
    {
        /** @var $currentToken OAuthToken */
        $currentToken = $this->container->get('security.token_storage')->getToken();
        /** @var $currentUser UserInterface */
        $currentUser = $currentToken->getUser();

        /** @var $resourceOwner ResourceOwnerInterface */
        $resourceOwner = $this->getResourceOwnerByName($service);
        /** @var $userInformation UserResponseInterface */
        $userInformation = $resourceOwner->getUserInformation($accessToken);

        $event = new GetResponseUserEvent($currentUser, $request);
        $this->get('event_dispatcher')->dispatch(HWIOAuthEvents::CONNECT_CONFIRMED, $event);

        $this->container->get('hwi_oauth.account.connector')->connect($currentUser, $userInformation);

        if ($currentToken instanceof OAuthToken) {
            // Update user token with new details
            $newToken =
                is_array($accessToken) &&
                (isset($accessToken['access_token']) || isset($accessToken['oauth_token'])) ?
                    $accessToken : $currentToken->getRawToken();

            $this->authenticateUser($request, $currentUser, $service, $newToken, false);
        }

        if (null === $response = $event->getResponse()) {
            if ($targetPath = $this->getTargetPath($request->getSession())) {
                $response = $this->redirect($targetPath);
            } else { dump('eeeee');
                /*$response = $this->render('@HWIOAuth/Connect/connect_success.html.twig', array(
                    'userInformation' => $userInformation,
                    'service' => $service,
                ));*/
                $this->addFlash('notice','Welcome amongst us customer');
                $this->redirectToRoute('homepage');
            }
        }

        $event = new FilterUserResponseEvent($currentUser, $request, $response);
        $this->get('event_dispatcher')->dispatch(HWIOAuthEvents::CONNECT_COMPLETED, $event);

        return $response;
    }
}
