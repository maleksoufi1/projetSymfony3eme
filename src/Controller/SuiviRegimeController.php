<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Regime;
use App\Entity\SuiviRegime;
use App\Form\SuiviRegimeType;
use App\Repository\CalendarRepository;
use App\Repository\SuiviRegimeRepository;
use DateTime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\VarDumper\Cloner\Data;

class SuiviRegimeController extends AbstractController
{
    /**
     * @Route("/suiviRegime", name="suivi_regime")
     */
    public function index(): Response
    {
        return $this->render('suivi_regime/index.html.twig', [
            'controller_name' => 'SuiviRegimeController',
        ]);
    }
     

     /**
     * @Route("/listsuiviRegimes", name="listsuiviRegimes")
     * @IsGranted("ROLE_NUTRIONNISTE")
     */
    public function listsuiviRegimes(): Response
    {
        $rep = $this->getDoctrine()->getRepository(SuiviRegime::class);
        $suiviRegimes = $rep->findAll();
 
        return $this->render('suivi_regime/listsuiviRegimes.html.twig', [
          'suiviRegimes'=>$suiviRegimes,
     ]);
        
    }


     /**
     * @Route("/addsuiviRegime", name="addsuiviRegime")
     * @IsGranted("ROLE_NUTRIONNISTE","ROLE_CLIENT","ROLE_ADMIN")
     */
    public function addsuiviRegime(Request $request): Response
    {
       

        $suiviRegime = new SuiviRegime();
        
        $form = $this->createForm(SuiviRegimeType::class , $suiviRegime);
        $form = $form->handleRequest($request);

        if ($form->isSubmitted()&& $form->isValid()){
            $suiviRegime = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($suiviRegime);
            $em->flush();
            return $this->redirectToRoute('listsuiviRegimes');
        }


        return $this->render('suivi_regime/addsuiviRegime.html.twig', [
            'formAddsuivisRegime'=>$form->createView(),
        ]);
    }


    /**
     * @Route("/updatesuiviRegime/{id}", name="updatesuiviRegime")
     * @IsGranted("ROLE_NUTRIONNISTE","ROLE_CLIENT")
     */
    public function updatesuiviRegime(Request $request , $id): Response
    {
        $rep = $this->getDoctrine()->getRepository(SuiviRegime::class);
        $suiviRegime  = $rep->find($id);
        $form = $this->createForm(SuiviRegimeType::class , $suiviRegime);
        $form = $form->handleRequest($request);
        if ($form->isSubmitted()&& $form->isValid()){
          
             $em = $this->getDoctrine()->getManager();
             $em->flush();
             return $this->redirectToRoute('listsuiviRegimes');
         }
 
        return $this->render('suivi_regime/updatesuiviRegime.html.twig', [
            'formUpsuiviRegime'=> $form->createView(),
     ]);
        
    }

    /**
     * @Route("/deletesuiviRegime/{id}", name="deletesuiviRegime")
     * @IsGranted("ROLE_NUTRIONNISTE","ROLE_CLIENT")
     */
    public function deletesuiviRegime($id): Response
    {
        $rep = $this->getDoctrine()->getRepository(SuiviRegime::class);
        $em = $this->getDoctrine()->getManager();
        $suiviRegime = $rep->find($id);
        $em->remove($suiviRegime);
        $em->flush();
        return $this->redirectToRoute('listsuiviRegimes');
    }





    /**
     * @Route("/addsuiviRegimeDirect/{id}", name="addsuiviRegimeDirect")
     * @IsGranted("ROLE_NUTRIONNISTE","ROLE_CLIENT",)
     */
    public function addsuiviRegimeDirect($id,Request $request,SuiviRegimeRepository $suiviRegimeRepository): Response
    {
        //va etre variable session
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->find($this->getUser());
        $user_id=$user->getId();
        $rep = $this->getDoctrine()->getRepository(User::class);
        $user = $rep->find($user_id);
        $userRegime= $suiviRegimeRepository->findSuiviByIdUser($user_id);
        //verifier si user courant a déja un suivi régime ou non 
        
        if($userRegime == null){
             //recuperation de regime par id 
        $rep = $this->getDoctrine()->getRepository(Regime::class);
        $regime = $rep->find($id);

        

        //creation de suivi
        $suiviRegime = new SuiviRegime();
        $suiviRegime->setRegime($regime);
        $suiviRegime->setUser($user);
        $em = $this->getDoctrine()->getManager();
        $em->persist($suiviRegime);
        $em->flush();
            return $this->redirectToRoute('showsuiviRegime');
        }    
        
        return $this->render('suivi_regime/suiviRegimeExist.html.twig', [
            'user'=> $user,
     ]);
   
    }

     



      /**
     * @Route("/showsuiviRegime", name="showsuiviRegime")
     * @IsGranted("ROLE_NUTRIONNISTE","ROLE_CLIENT")
     */
    public function showsuiviRegime(SuiviRegimeRepository $suiviRegimeRepository,CalendarRepository $calendarRep): Response
    {
        //va etre variable session
        
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->find($this->getUser());
        $user_id=$user->getId();

        $suiviRegime = $suiviRegimeRepository->findSuiviByIdUser($user_id);
     
        $events = $calendarRep->findCalendarSuivi($suiviRegime);
        $rdvs = [];
        foreach($events as $event){
            $rdvs[]=[
                'id' => $event->getId(),
                'start' => $event->getStart()->format('Y-m-d H:i:s'),
                'end' => $event->getEnd()->format('Y-m-d H:i:s'),
                'title' => $event->getTitle(),
                'description' => $event->getDescription(),
                'backgroundColor' => $event->getBackgroundColor(),
                'borderColor' => $event->getBorderColor(),
                'textColor' => $event->getTextColor(),
                'allDay' => $event->getAllday(),
            ];
        }
        $data = json_encode($rdvs);
     
       
        //pour laffichage du modal
        $repas = $calendarRep->findCalendarSuivi($suiviRegime );
      

      
        return $this->render('suivi_regime/showsuiviRegimes.html.twig', [
          'suiviRegime'=>$suiviRegime,'data'=>$data ,'repas'=>$repas
     ]);


        
    }

    

 /**
     * @Route("/listsuiviParRegime/{id}", name="listsuiviParRegime")
     * @IsGranted("ROLE_NUTRIONNISTE","ROLE_ADMIN")
     */
    public function listsuiviParRegime($id,SuiviRegimeRepository $suiviRegimeRepository): Response
    {
        $suiviRegimes = $suiviRegimeRepository->findListSuivisByIdRegime($id);
 
        return $this->render('suivi_regime/listsuiviParRegime.html.twig', [
          'suiviRegimes'=>$suiviRegimes,
     ]);
        
    }



}
