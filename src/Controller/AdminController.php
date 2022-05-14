<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
class AdminController extends AbstractController
{
   /**
     * @Route("/statistiqueAdmin", name="stats")
     * @IsGranted("ROLE_ADMIN")
     */
    public function statistiques(){
        // statistique 
        $users =$this->getDoctrine()->getRepository(User::class)->findAll();
        
        return $this->render('statAdmin/statistiqueAdmin.html.twig',[
          'users' => $users   
        ]);
    }

    /**
    * @Route("/bloqueclient/{email}", name="blocage")
    * @IsGranted("ROLE_ADMIN")
    */
    public function ban(String $email): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $users = $this->getDoctrine()->getRepository(User::class)->findBy(array('email' => $email));
            $user = $users[0];
            $user->setRole("bloque"); 
            $user->setRoles(["ROLE_BLOQUE"]);
        $entityManager->flush();
        return $this->redirectToRoute('listClient');
    }
    /**
    * @Route("/debloqueclient/{email}", name="deblocage")
      *@IsGranted("ROLE_ADMIN")
     */
    public function deblocage(String $email): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $users = $this->getDoctrine()->getRepository(User::class)->findBy(array('email' => $email));
            $user = $users[0];
            $user->setRole("Client"); 
            $user->setRoles(["ROLE_CLIENT"]);   
            $entityManager->flush();
            return $this->redirectToRoute('listClient');
    }
   
    /**
     * @Route("/bloque", name="bloque")
      *@IsGranted("ROLE_BLOQUE")
     */
    public function page404(){
    
      return $this->render('client/bloque.html.twig',[
        
      ]);
  }
    /**
     * @Route("/resetpwd", name="app_resetpwd")
     */
    public function index2(): Response
    {
        return $this->render('reset_password/rstpwd.html.twig', [
            'controller_name' => 'BaseController',
        ]);
    }
  
}
