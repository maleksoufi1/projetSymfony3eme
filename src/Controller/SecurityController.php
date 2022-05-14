<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->IsGranted('ROLE_CLIENT')){
            return $this->redirectToRoute('accueil');
            
        }
        else if ($this->IsGranted('ROLE_COACH') )
        {

            return $this->redirectToRoute('listProgramme'); 
        }
        else if ($this->IsGranted('ROLE_NUTRIONNISTE') )
        {

            return $this->redirectToRoute('listRegimes'); 
        
        }
        else if ($this->IsGranted('ROLE_BLOQUE') )
        {

            return $this->redirectToRoute('bloque'); 
        
        }
        else if ($this->IsGranted('ROLE_ADMIN') )
        {

            return $this->redirectToRoute('listCoach'); 
        
        }
       

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
