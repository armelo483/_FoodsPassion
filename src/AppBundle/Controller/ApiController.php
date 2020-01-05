<?php


namespace AppBundle\Controller;


use AppBundle\Entity\Mets;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\View;

class ApiController extends Controller
{

    /**
     * @Get(
     *     path = "/mets/{id}",
     *     name = "app_article_show",
     *     requirements = {"id"="\d+"}
     * )
     * @View
     */
    public function showAction(Mets $article)
    {
        return $article;
    }
}


