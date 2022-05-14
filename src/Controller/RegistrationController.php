<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Mailer\MailerInterface;
use App\Form\UserType;
use App\Form\AjouterUserType;
use App\Form\ModifierUserType;
use App\Repository\UserRepository;
use App\Security\UsersAuthenticator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Symfony\Component\Security\Core\Encoder\AuthenticatorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Security\LoginFormAuthentificatorAuthenticator;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;



class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $userPasswordEncoder, EntityManagerInterface $entityManager, GuardAuthenticatorHandler $guardHandler, LoginFormAuthentificatorAuthenticator $authenticator,MailerInterface $mailer): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
          
            $user->setRoles(["ROLE_CLIENT"]);
            $user->setRole("client");
            // encode the plain password
            $user->setPassword(
            $userPasswordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            // On génère un token et on l'enregistre
            $user->setActivationToken(md5(uniqid()));
            

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email
        // On crée le message
        $message =(new TemplatedEmail())
        // On attribue l'expéditeur
        ->from(new Address('adomifit.123@gmail.com', 'A Domifit'))
        // On attribue le destinataire
       
        
        ->to($user->getEmail())
        ->subject('Activation compte')
        // On crée le texte avec la vue
        ->htmlTemplate('emails/activation.html.twig')
        ->context([
            'token' => $user->getActivationToken(),
        ])
    ;
        $mailer->send($message);
                // do anything else y   ou need here, like send an email

                return $guardHandler->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $authenticator,
                    'main' // firewall name in security.yaml
                );
                    return $this->redirectToRoute('accueil');
                }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }


    /**
 * @Route("/activation/{token}", name="activation")
 */
public function activation($token, UserRepository $users)
{
    // On recherche si un utilisateur avec ce token existe dans la base de données
    $user = $users->findOneBy(['activation_token' => $token]);

    // Si aucun utilisateur n'est associé à ce token
    if(!$user){
        // On renvoie une erreur 404
        throw $this->createNotFoundException('Cet utilisateur n\'existe pas');
    }

    // On supprime le token
    $user->setActivationToken(null);
    $entityManager = $this->getDoctrine()->getManager();
    $entityManager->persist($user);
    $entityManager->flush();

    // On génère un message
    $this->addFlash('message', 'Utilisateur activé avec succès');

    // On retourne à l'accueil
    return $this->redirectToRoute('accueil');
}
}
