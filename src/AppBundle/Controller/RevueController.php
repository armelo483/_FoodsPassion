<?php


namespace AppBundle\Controller;


use AppBundle\Entity\Commentaires;
use AppBundle\Entity\Mets;
use Panier\EcommerceBundle\Entity\Commande;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RevueController extends Controller
{
    private $idMet;
    /**
 * @Route("/revue/{idMet}", name="revue",requirements={"idMet"="\d+"}, defaults={"idMet"=1})
 */

    public function indexAction(Request $request, $idMet){

        //var_dump($request->request); exit;
        if($idMet != 0) {
            $met = $this->getDoctrine()
                ->getRepository(Mets::class)
                ->find($idMet);
        }else{
            $idMet = $request->request->get('idMet');
            $met = $this->getDoctrine()
                ->getRepository(Mets::class)
                ->find($idMet);
        }



        $commentaires = $this->getDoctrine()
                                ->getRepository(Commentaires::class)
                                ->findBy(array('mets' => $met));

        $notation = $request->request->get('notation');
        if($notation <= 0)


        if ($request->request->get('idMet') != '') {
            $commentaires = new Commentaires();

            $entityManager = $this->getDoctrine()->getManager();

            $pseudo = random_int(0, 195);
            //$username = $this->get('security.context')->getToken()->getUser()->getUsername();
            $commentaires->setNotation($notation);
            //$commentaires->setAuteur($username);
            $commentaires->setAuteur('username'.$pseudo);

            $comment = $request->request->get('comment');
            $commentTitre = $request->request->get('titre_comment');

            $commentaires->setNotation($notation);
            $commentaires->setCommentaires($comment);
            $commentaires->setMets($met);
            $commentaires->setTitreCommentaires($commentTitre);

            $entityManager->persist($commentaires);
            $entityManager->flush();

            return $this->redirectToRoute('revue', array('idMet' => $idMet));

        }

        return $this->render('index/revue.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
            'met' => $met,
            'commentaires' => $commentaires
        ]);
    }

    /**
     * @Route("/addcomment", name="revue_comment")
     */

    public function addcommentAction(Request $request){







        }


}