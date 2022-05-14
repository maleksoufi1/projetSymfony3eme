<?php

namespace App\Controller;
use App\Form\ChangeEmailType;
use App\Form\ChangeTelType;
use App\Form\ChangeNomPrenomType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Form\EditProfileType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


use Symfony\Component\Form\FormError;

use App\Form\ResetPasswordType;

use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class ProfilController extends AbstractController
{
    /**
     * @Route("/profil", name="profil")
     */
    
    public function index(Request $request )
    {
        
        $user = $this->getUser();
        $form = $this->createForm(ChangeNomPrenomType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('message', 'Profil mis à jour');
            return $this->redirectToRoute('profil');
        }

       

        return $this->render('profil/index.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
    

   
 
    /**
     * @Route("/passmodifier", name="users_pass_modifier")
     */
    public function editAction(Request $request , UserPasswordEncoderInterface $passwordEncoder)

    {
        // Modifier mot de passe

    	if($request->isMethod('POST')){
            $em = $this->getDoctrine()->getManager();

            $user = $this->getUser();
            $p = $this->getUser()->getPassword();
            
            // On vérifie si les 2 mots de passe sont identiques

            if(  $request->request->get('pass') == $request->request->get('pass2')){
                $user->setPassword($passwordEncoder->encodePassword($user, $request->request->get('pass')));
                $em->flush();
                $this->addFlash('message', 'Mot de passe mis à jour avec succès');

                return $this->redirectToRoute('profil');
            }else{
                $this->addFlash('error', 'Ancien mdp incorect ou Les deux mots de passe ne sont pas identiques ');
            }
        }

    	return $this->render('profil/EditPassword.html.twig', array(

    	));

    }
}
