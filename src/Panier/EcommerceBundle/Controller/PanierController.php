<?php

namespace Panier\EcommerceBundle\Controller;


use AppBundle\Entity\Mets;
use AppBundle\Repository\MetsRepository;
use Panier\EcommerceBundle\Entity\Commande;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints\Email as EmailConstraint;


class PanierController extends Controller
{
    public function indexAction(Request $request)
    {
        $panier = $request->getSession()->get('panier');
        $entityManager = $this->getDoctrine()->getManager();
        $commande = new Commande();
        $metsRepository = $this->getDoctrine()->getRepository(Mets::class);
        foreach($panier as $idMet=>$qte){
            $met = $metsRepository->find($idMet);
            $commande->addMet($met);
        }
        $commande->setStatus('en cours');
        $commande->setOwner('UserXRRR');


        $entityManager->persist($commande);
        $entityManager->flush();
        $idCommand = $commande->getId()->toString();
        $request->getSession()->set('idCommand',$idCommand);
       //


        return $this->render('PanierEcommerceBundle:Panier:index.html.twig',['commandes' => $commande->getMets(),
            'active' => 1]);
    }



    public function identiteAction(Request $request){

        $parametersArr = $request->request->all();
        $confirmedCommandeArr = [];
        $nbIteration = 0;
        $obj = new \stdClass;
        array_walk($parametersArr,function ($item, $key)  use(&$nbIteration, $obj, &$confirmedCommandeArr) {
            $nbIteration++;

            $keyLibelleArr = preg_split("/_/",$key);
            $idItem = $keyLibelleArr[1];
            $keyLibelle = $keyLibelleArr[0];
           
            $obj->$keyLibelle =$item;

            if($nbIteration%2 == 0){
                $confirmedCommandeArr[$idItem] = clone $obj;
            }


        });
        

       $request->getSession()->set('recapCommande', $confirmedCommandeArr);

        return $this->render('PanierEcommerceBundle:Panier:identite.html.twig', ['active' => 2]);
    }

    public function isValidPhoneNumber($phoneNumber){

        return preg_match("/^(?:(?:\+|00)33|0)\s*[1-9](?:[\s.-]*\d{2}){4}$/", $phoneNumber)!=false;
    }

    public function isValidEmail($email){
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public function submitIdentiteAction(Request $request){

        if ($request->isMethod('post')) {

            $email = $request->request->get('email');
            $tel = $request->request->get('tel');
            $errorMessageMail = '';
            $errorMessageTel = '';

            $isValidEmail = $this->isValidEmail($email);
            $isValidTel = $this->isValidPhoneNumber($tel);
            $idCommand = $request->getSession()->get('idCommand');
            $commandeRepository = $this->getDoctrine()->getRepository(Commande::class);
            $entityManager = $this->getDoctrine()->getManager();

            if ($isValidEmail && $isValidTel) {

                $commande = $commandeRepository->find($idCommand);
                $commande->setOwner($email);
                $commande->setTel($tel);

                $request->getSession()->set('email',$email);
                $request->getSession()->set('tel',$tel);

                $entityManager->merge($commande);
                $entityManager->flush();

                //Pour que ca redirige en "POST" car par défaut redirige en "GET"
                return $this->redirectToRoute('panier_paiement', [
                    'request' => $request
                ], 307);


            } else {
                // this is *not* a valid email address
                //$errorMessage = $errors[0]->getMessage();
                if(!$isValidTel)
                    $errorMessageTel = 'Please enter a valid french mobile';
                if(!$isValidEmail)
                  $errorMessageMail = 'Invalid email address';

                return $this->render('PanierEcommerceBundle:Panier:identite.html.twig', ['active' => 2, 'errorMessageMail'=>$errorMessageMail, 'errorMessageTel' => $errorMessageTel]);

            }

        }

        return $this->render('PanierEcommerceBundle:Panier:identite.html.twig', ['active' => 2]);

    }

    //Afficher le recap avant e paiement effectif
    public function paiementAction(Request $request){

        $recapCommande = $request->getSession()->get('recapCommande');
        $entityManager = $this->getDoctrine()->getManager();
        $metsRepository = $this->getDoctrine()->getRepository(Mets::class);
        $commande = new Commande();
        $qteCommandeArray = [];
        $prixCommandeArray = [];
        foreach($recapCommande as $key=>$val){

            $met = $metsRepository->find($key);
            $commande->addMet($met);
            array_push($qteCommandeArray, $val->qteCommandee);
            array_push($prixCommandeArray, $val->prixUnitaire*$val->qteCommandee);

        }
        $prixTotal = array_sum($prixCommandeArray);
        array_push($prixCommandeArray,$prixTotal);
        $qteTotal = array_sum($qteCommandeArray);
        array_push($qteCommandeArray,$qteTotal);

        return $this->render('PanierEcommerceBundle:Panier:paiement.html.twig', ['active' => 4, 'commandes' => $commande->getMets(),
            'prixCommandeArray'=>$prixCommandeArray, 'qteCommandeArray'=>$qteCommandeArray]);

    }
    public function menuAction()
    {
        $session = $this->getRequest()->getSession();
        if (!$session->has('panier'))
            $articles = 0;
        else
            $articles = count($session->get('panier'));

        return $this->render('EcommerceBundle:Default:panier/modulesUsed/panier.html.twig', array('articles' => $articles));
    }


    //Réaliser le paiement avec stripe checkout
    public function checkoutAction(Request $request){
        $error = false;
        $idCommand = $request->getSession()->get('idCommand');
        $commandeRepository = $this->getDoctrine()->getRepository(Commande::class);
        $entityManager = $this->getDoctrine()->getManager();

        if ($request->isMethod('post')){
           $token = $request->request->get('stripeToken');
           $totalAmount = $request->request->get('totalAmount');
           try{
                \Stripe\Stripe::setApiKey("sk_test_6RCLaSMPneQh55qKkCkzIQBU");
               $charge = \Stripe\Charge::create([
                   'amount' => $totalAmount,
                   'currency' => 'eur',
                   'description' => 'Example charge',
                   'source' => $token,
               ]);
           }catch(\Stripe\Error\Card $exception){
                $error = $exception->getMessage();
           }
        }

        if(!$error){
            $commande = $commandeRepository->find($idCommand);
            $commande->setStatus('terminé');
            $entityManager->merge($commande);
            $entityManager->flush();
            return $this->redirectToRoute('panier_confirmationPaiement',[
                'request' => $request
            ], 307);

        }else{
            $this->addFlash('echec','Paiement non abouti :(');

        }

        return $this->redirectToRoute('panier_checkout',[
            'request' => $request
        ], 307);
    }




    public function confirmationPaiementAction(Request $request){
       //dump($request->request); die;
        $email = $request->getSession()->get('email');

        $request->getSession()->clear();
        $this->addFlash('success','Bravo votre paiement a bien été effectué, merci pour votre achat');

        return $this->render('PanierEcommerceBundle:Panier:confirmationpaiement.html.twig', ['email' => $email]);

    }

    public function removeAction(Request $request){

        $session = $request->getSession();
        $metId = $request->query->get('metId');
        if (!$session->has('panier')) $session->set('panier',array());
        $panier = $session->get('panier');

        if (array_key_exists($metId, $panier)) {

            unset($panier[$metId]);
        }

        $session->set('panier', $panier);


        //return $this->redirect($this->generateUrl('panier'));
        return new JsonResponse(['success'=>$session->get('panier')]);
    }
    public function ajouterAction(Request $request)
    {

        $session = $request->getSession();
        $metId = $request->query->get('metId');
        if (!$session->has('panier')) $session->set('panier',array());
        $panier = $session->get('panier');

        if (array_key_exists($metId, $panier)) {
            if ($request->query->get('qte')!= null) $panier[$metId] = $request->query->get('qte');

        } else {
            if ($metId != null)
                //$panier[$id] = $this->getRequest()->query->get('qte');
                $panier[$metId] = 1;
        }

        $session->set('panier', $panier);


        //return $this->redirect($this->generateUrl('panier'));
        return new JsonResponse(['success'=>$session->get('panier')]);
    }
}
