<?php


namespace Panier\EcommerceBundle\Controller;

use FOS\RestBundle\Request\ParamFetcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ApiController extends  Controller
{

    /**
     * @Rest\Get("api/articles", name="app_article_list")
     * @Rest\QueryParam(name="order")
     * @View
     */
    public function listAction(ParamFetcherInterface $paramFetcher)
    {
        dump($paramFetcher->get('order'));
    }

}