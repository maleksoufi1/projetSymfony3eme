<?php

namespace App\Controller;
use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class ContactController extends AbstractController
{
    /**
     * @Route("/contacter", name="contacter")
     */
    public function index(Request $request, \Swift_Mailer $mailer)
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $contact = $form->getData();

            // Ici nous enverrons l'e-mail
            $message = (new \Swift_Message('Nouveau contact'))
    // On attribue l'expéditeur
    ->setFrom($contact['email'])

    // On attribue le destinataire
    ->setTo('khalil.turki@esprit.tn')

    // On crée le texte avec la vue
    ->setBody(
        $this->renderView(
            'emails/contact.html.twig', compact('contact')
        ),
        'text/html'
    )
;
//Et enfin, on envoie le message

     $mailer->send($message);



            $this->addFlash('message', 'Votre message a été transmis, nous vous répondrons dans les meilleurs délais.'); 
            
            return $this->render('contact/success.html.twig');
        
        }
        return $this->render('contact/index.html.twig',['contactForm' => $form->createView()]);
        
    }
    
}
