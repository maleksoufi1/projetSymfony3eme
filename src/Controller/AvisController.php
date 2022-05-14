<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Avis;
use App\Form\AvisType;
use App\Entity\Evenement;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
class AvisController extends AbstractController
{
    /**
     * @Route("/avis", name="avis")
     */
    public function index(): Response
    {
        return $this->render('avis/index.html.twig', [
            'controller_name' => 'AvisController',
        ]);
    }

/**
* @Route("/listAvis", name="listAvis")

*/
public function list(): Response
{
   $rep=$this->getDoctrine()->getRepository(Avis::class);

   $avis =$rep-> findAll();

   return $this->render('evenement/index1.html.twig', [
       'controller_name' => 'AvisController',
       'avis' => $avis,
   ]);

   
}

    /**
     * @Route("/addAvis/{id}", name="addAvis" , methods={"GET","POST"}) 
     */
    public function addAvis(Request $request , $id): Response
    {
        
        $evenement= $this->getDoctrine()->getRepository(Evenement::class)->find($id);
        
        $avis = new Avis();
        
        if($request->isMethod('POST')){
            $commentaire = $request->request->get('commentaire') ;
            $note = $request->request->get('note') ;
            $avis->setComentaire($commentaire);
            $avis->setNote($note);
            $avis->setEvenementId($evenement);
            $em = $this->getDoctrine()->getManager();
            $em->persist($avis);
            $em->flush();
            return $this->redirectToRoute('listEvenement');
            
        }
        return $this->render('evenement/stars.html.twig', array(

        ));
    }


   
}
