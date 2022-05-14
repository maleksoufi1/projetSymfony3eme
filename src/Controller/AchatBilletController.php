<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\AchatBilletType;
use App\Entity\Billet;
use App\Entity\User;
use App\Entity\AchatBillet;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


class AchatBilletController extends AbstractController
{
  
    /**
     * @Route("/AchatBillet/{id}", name="achatBillet" , methods={"GET","POST"})
     * @IsGranted("ROLE_CLIENT")
     */
    public function addAchatBillet(Request $request, $id ): Response
    {

        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->getDoctrine()->getRepository(User::class)->find($this->getUser());
        $billet = $this->getDoctrine()->getRepository(Billet::class)->find($id);

        $achatBillet = new AchatBillet();

        $form = $this->createForm(AchatBilletType::class , $achatBillet);
        $form = $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()){
            $achatBillet = $form->getData();
            $achatBillet->setUser($user);
            $achatBillet->setBillet($billet);
            $achatBillet->setUser($user);
            $achatBillet->setDateAchat(new \DateTIme('now'));
             $billet->setQuantite(($billet->getQuantite()- $achatBillet->getQuantite()));
            $em = $this->getDoctrine()->getManager();
            $em->persist($achatBillet);
            $em->flush();
            return $this->redirectToRoute('listCategorieF');
           
        }


        return $this->render('Achat_billet/index.html.twig', [
            'formA'=>$form->createView(),
        ]);
    }
}
