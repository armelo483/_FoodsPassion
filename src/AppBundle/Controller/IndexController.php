<?php
/**
 * Created by PhpStorm.
 * User: alain.gonze
 * Date: 21/05/2019
 * Time: 16:18
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Mets;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class IndexController extends Controller
{

    private $tokenManager;

    public function __construct(CsrfTokenManagerInterface $tokenManager = null)
    {
        $this->tokenManager = $tokenManager;
    }
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $session = $request->getSession();
        $launchAnimationOnce = '';
        $onceAnimation = '';
        $redirectPath = $request->request->get('_target_path');
        //dump($request);die;
        $securityContext = $this->container->get('security.authorization_checker');
        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $launchAnimationOnce = 1;
        }

        if (!empty($session->get('onceAnimation'))) {

            $onceAnimation = (int)$session->get('onceAnimation');
            $session->set('onceAnimation', null);
        }


        $mets = $this->getDoctrine()
            ->getRepository(Mets::class)
            ->findAll();

        // replace this example code with whatever you need
        return $this->render('index/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
            'mets' => $mets,
            'launchAnimationOnce' => $launchAnimationOnce,
            'onceAnimation' => $onceAnimation,
        ]);
    }


}