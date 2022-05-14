<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use  App\Entity\CommentaireUser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class CommentaireUserController extends AbstractController
{
    /**
     * @Route("/commentaire/user", name="commentaire_user")
     */
    public function index(): Response
    {
        return $this->render('commentaire_user/index.html.twig', [
            'controller_name' => 'CommentaireUserController',
        ]);
    }
     /**
     * @Route("/rating", name="rating",methods={"POST"})
     */
 public function rating(Request $request )
 {
    $em = $this->getDoctrine()->getManager();
    $requestValue = $request->get('k');
    $requestUser = $request->get('a');
    $requestCommentaire = $request->get('c');
   
     $like=new CommentaireUser();
      $like->setValue($requestValue);
      $like->setCommentaire($requestValue);
      $like->setUser($requestValue);
    $em->persist($like);
    $em->flush();
 }
}
