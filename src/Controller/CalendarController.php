<?php

namespace App\Controller;

use DateTime;
use App\Entity\Calendar;
use App\Form\CalendarType;
use App\Repository\CalendarRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\SuiviRegimeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
/**
 * @Route("/calendar")
 */
class CalendarController extends AbstractController
{
    /**
     * @Route("/testCalendar", name="testCalendar", methods={"GET"})
     * @IsGranted("ROLE_NUTRIONNISTE","ROLE_ADMIN","ROLE_CLIENT")
     */
    
    public function testCalendar(CalendarRepository $calendar): Response
    {
        $events = $calendar->findAll();
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
        return $this->render('calendar/index.html.twig', ['data'=>$data,
        'calendars' => $calendar->findAll(),
         
        ]);
    }
    
    /**
     * @Route("/", name="calendar_index", methods={"GET"})
     */
    public function index(CalendarRepository $calendarRepository): Response
    {
       
        return $this->render('calendar/index.html.twig', [
            'calendars' => $calendarRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new/{id}", name="calendar_new", methods={"GET", "POST"})
     * @IsGranted("ROLE_NUTRIONNISTE","ROLE_ADMIN","ROLE_CLIENT")
     */
    public function new(Request $request,  SuiviRegimeRepository $suiviRegimeRepository ,$id): Response
    {

        $suiviRegime =$suiviRegimeRepository->find($id) ;
        $calendar = new Calendar();

        $form = $this->createForm(CalendarType::class, $calendar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $calendar = $form->getData();
            $calendar->setSuiviRegime($suiviRegime);
            $em = $this->getDoctrine()->getManager();
            $em->persist($calendar);
            $em->flush();
            return $this->redirectToRoute('CalendarSuivi', ['id' => $suiviRegime->getId()]);
       
        }

        return $this->render('calendar/new.html.twig', [
            'calendar' => $calendar,
            'formAdd' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="calendar_show", methods={"GET"})
     */
    public function show(Calendar $calendar): Response
    {
        return $this->render('calendar/show.html.twig', [
            'calendar' => $calendar,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="calendar_edit", methods={"GET", "POST"})
     * @IsGranted("ROLE_NUTRIONNISTE","ROLE_ADMIN")
     */
    public function edit(Request $request, $id): Response
    {
        $rep = $this->getDoctrine()->getRepository(Calendar::class);
        $calendar  = $rep->find($id);
        $suiviRegime = $calendar->getSuiviRegime();
        $form = $this->createForm(CalendarType::class, $calendar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('CalendarSuivi', ['id' => $suiviRegime->getId()]);
        }

        return $this->render('calendar/edit.html.twig', [
            'calendar' => $calendar,
            'formUpdate' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="calendar_delete", methods={"POST"})
     * @IsGranted("ROLE_NUTRIONNISTE","ROLE_ADMIN")
     */
    public function delete($id): Response
    {
        $rep = $this->getDoctrine()->getRepository(Calendar::class);
        $em = $this->getDoctrine()->getManager();
        $calendar = $rep->find($id);
        $suiviRegime = $calendar->getSuiviRegime();
        $em->remove($calendar);
        $em->flush();

        return $this->redirectToRoute('CalendarSuivi', ['id' => $suiviRegime->getId()]);
    }





    


     /**
     * @Route("/CalendarSuivi/{id}", name="CalendarSuivi", methods={"GET"})
     * @IsGranted("ROLE_NUTRIONNISTE","ROLE_ADMIN","ROLE_CLIENT")
     */
    
    public function CalendarSuivi($id,CalendarRepository $calendar , SuiviRegimeRepository $suiviRegimeRepository): Response
    {
        $suiviRegime =$suiviRegimeRepository->find($id) ;
        $events = $calendar->findCalendarSuivi($id);
        
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
        return $this->render('calendar/affichCalendarSuivi.html.twig', ['data'=>$data,
        'calendars' => $events,'suiviRegime' => $suiviRegime,
         
        ]);
    }





    
    /**
     * @Route("/upClendar/{id}", name="upClendar", methods={"PUT"})
     * @IsGranted("ROLE_NUTRIONNISTE","ROLE_ADMIN")
     */

     // ?Clendar un objet potentiel : si nexiste pas 
    public function updateCalendarAjax(?Calendar $calendar, Request $request ,CalendarRepository $calendarRepository): Response
    {
       //recuperer les donner envyer par fullcalendar 
       $donnees = json_decode($request->getContent()); 
       if(
        //verifier donnees existe et non vide
           isset($donnees->title) && !empty($donnees->title)&&
           isset($donnees->start) && !empty($donnees->start)&&
           isset($donnees->description) && !empty($donnees->description)&&
           isset($donnees->backgroundColor) && !empty($donnees->backgroundColor)&&
           isset($donnees->borderColor) && !empty($donnees->borderColor)&&
           isset($donnees->textColor) && !empty($donnees->textColor)
       ){

           $code = 200; //initializer pour dirait c bon cree
           // verif si l'id existe
           if(!$calendar){
               //instancier rdv
               $calendar = new Calendar;
               $code =201;
           }
           $calendar->setTitle($donnees->title);
           $calendar->setDescription($donnees->description);
           $calendar->setStart(new DateTime($donnees->start));
           //test allday

           if($donnees->allDay){
               //si true date debut = date fin
            $calendar->setEnd(new DateTime($donnees->start));
           }else{
            $calendar->setEnd(new DateTime($donnees->end));  
           }
           $calendar->setAllday($donnees->allDay);
           $calendar->setBackgroundColor($donnees->backgroundColor);
           $calendar->setBorderColor($donnees->borderColor);
           $calendar->setTextColor($donnees->textColor);

           $em = $this->getDoctrine()->getManager();
           $em->persist($calendar);
           $em->flush();

           return new Response('Update valide',$code);
       }else{
           return new Response('Données incompléte',404);
       }
        return $this->render('calendar/index.html.twig', [
            'calendars' => $calendarRepository->findAll(),
        ]);
    }







}
