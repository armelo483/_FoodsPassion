<?php
/**
 * Created by PhpStorm.
 * User: alain.gonze
 * Date: 26/06/2019
 * Time: 15:07
 */

namespace EcommerceBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ProduitController extends Controller
{
    public function detailProduitAction()
    {
        return $this->render('EcommerceBundle:Panier:panier.html.twig');
    }

    public function rechercheProduitAction()
    {
        return $this->render('EcommerceBundle:Panier:panier.html.twig');
    }
}