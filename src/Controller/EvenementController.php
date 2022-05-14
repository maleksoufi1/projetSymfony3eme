<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Evenement;
use App\Form\EvenementType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use App\Repository\EvenementRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


class EvenementController extends AbstractController
{
    /**
     * @Route("/evenement/{id}", name="evenement")
     */
    public function index($id): Response
    {
        $rep=$this->getDoctrine()->getRepository(Evenement::class);

   $evenements =$rep-> findByIdCategorie($id);
        return $this->render('evenement/index.html.twig',  [
       
            'evenements' => $evenements,
        ]);
    }
    /**
     * @Route("/map/{id}", name="map")
     */
    public function indexm(Request $request , $id): Response
    {
        $rep=$this->getDoctrine()->getRepository(Evenement::class);

   $evenements =$rep-> findByIddetail($id);
        return $this->render('evenement/map.html.twig',['evenements' => $evenements]);
    }

/**
     * @Route("/listEvenements", name="listEvenements")
     */
    public function listEvenements(): Response
    {
        

        return $this->render('evenement/index1.html.twig', [
            'controller_name' => 'EvenementController',
            
        ]);
    }
      /**
     * @Route("/listEvenement", name="listEvenement")
     * @IsGranted("ROLE_ADMIN")
     */
    public function list(): Response
    {
        $rep=$this->getDoctrine()->getRepository(Evenement::class);

        $evenements =$rep-> findAll();

        return $this->render('evenement/list.html.twig', [
            'controller_name' => 'EvenementController',
            'evenements' => $evenements,
        ]);
    }
/**
     * @Route("/listEvenementD", name="listEvenementD")
     *  @IsGranted("ROLE_ClIENT")
     */
    public function listD(): Response
    {
        $rep=$this->getDoctrine()->getRepository(Evenement::class);

        $evenements =$rep-> findAll();

        return $this->render('evenement/index1.html.twig', [
            'controller_name' => 'EvenementController',
            'evenements' => $evenements,
        ]);
    }
    /**
     * @Route("/addEvenement", name="addEvenement")
     *  @IsGranted("ROLE_ADMIN")
     
     */
    public function addEvenement(Request $request,\Swift_Mailer $mailer): Response
    {

        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class , $evenement);
        $form = $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $evenement = $form->getData();
            $file = $form->get('image')->getData();
            $fileName = md5(uniqid()).'.'.$file->guessExtension();
            try{
                $file->move(
                $this->getParameter('images_directory'),
                $fileName
            );
            }catch(FileException $e){

            }
            $em = $this->getDoctrine()->getManager();
            $evenement->setImage($fileName);
            $em->persist($evenement);
            $em->flush();
            $message = (new \Swift_Message('Nouvel EVENEMENT!'))
        ->setFrom('myriambrahmi23@gmail.com')
        ->setTo('myriam.brahmi@esprit.tn')
        ->setBody("Bonjour Mr/M,
        Un evenement est ajouté sur le site . N'hésiter pas de checker notre site !")
    ;

    $mailer->send($message);
    $this->addFlash('message','Le message a bien été transmis');
            return $this->redirectToRoute('listEvenement');
        }


        return $this->render('evenement/addEvenement.html.twig', [
            'formAddEvenement'=>$form->createView(),
        ]);
    }


    /**
     * @Route("/updateEvenement/{id}", name="updateEvenement")
     *  @IsGranted("ROLE_ADMIN")
     */
    public function updateEvenement(Request $request , $id): Response
    {
        $rep = $this->getDoctrine()->getRepository(Evenement::class);
        $evenement  = $rep->find($id);
        $form = $this->createForm(EvenementType::class , $evenement);
        $form = $form->handleRequest($request);
        if ($form->isSubmitted() ){
            $file = $form->get('image')->getData();
            $fileName = md5(uniqid()).'.'.$file->guessExtension();
            try{
                $file->move(
                $this->getParameter('images_directory'),
                $fileName
            );
            }catch(FileException $e){

            }
          
             $em = $this->getDoctrine()->getManager();
             $evenement->setImage($fileName);
             $em->flush();
             return $this->redirectToRoute('listEvenement');
         }
 
        return $this->render('evenement/updateEvenement.html.twig', [
            'formUpdateEvenement'=> $form->createView(),
     ]);
        
    }

    /**
     * @Route("/deleteEvenement/{id}", name="deleteEvenement")
     *  @IsGranted("ROLE_ADMIN")
     */
    public function deleteEvenement($id): Response
    {
        $rep = $this->getDoctrine()->getRepository(Evenement::class);
        $em = $this->getDoctrine()->getManager();
        $evenement = $rep->find($id);
        $em->remove($evenement);
        $em->flush();
        return $this->redirectToRoute('listEvenement');
    }

    

                /**
                * @Route("/listEvenementC/{id}", name="listEvenementC")
                *  @IsGranted("ROLE_ADMIN","ROLE_CLIENT")
                */
public function listEvenementC(Request $request , PaginatorInterface $paginator, $id): Response
{
   $rep=$this->getDoctrine()->getRepository(Evenement::class);

   $donnees =$rep-> findByIdCategorie($id);
  
   $evenements = $paginator->paginate(
       $donnees,
       $request->query->getInt('page',1),
       2
   );

   return $this->render('evenement/index.html.twig', [
       
       'evenements' => $evenements,
   ]);
}

/**
* @Route("/listEvenementD/{id}", name="listEvenementD")
*  @IsGranted("ROLE_ADMIN","ROLE_CLIENT")
*/
public function listEvenementD(Request $request , $id): Response
{
   $rep=$this->getDoctrine()->getRepository(Evenement::class);

   $evenements =$rep-> findByIddetail($id);

   return $this->render('evenement/index1.html.twig', [
       
       'evenements' => $evenements,
   ]);
}



/**
     * @Route("/searchEvenementt", name="searchEvenementt")
     */
    public function searchEvenementt(Request $request , EvenementRepository $evenmentRepository){
        $em = $this->getDoctrine()->getManager();
        $requestString = $request->get('q');
        $posts =  $evenmentRepository->findEntitiesByStringe($requestString);
        if(!$posts) {
            $result['posts']['error'] = "Pas de catégorie !  ";
        } else {
            $result['posts'] = $this->getRealEntities($posts);
        }
        return new Response(json_encode($result));
    }
    public function getRealEntities($posts){
        foreach ($posts as $posts){
            $realEntities[$posts->getId()] = [$posts->getImage(),$posts->getTitre(),$posts->getDescription(),$posts->getHorraire()];

        }
        return $realEntities;
    }



    /**
    * @Route("/listEvenementE/{id}", name="listEvenementE")
    */
    public function listEvenementE(Request $request , $id): Response
    {
       $rep=$this->getDoctrine()->getRepository(Evenement::class);
    
       $evenements =$rep-> findByIddetail($id);
    
       return $this->render('evenement/stars.html.twig', [
           
           'evenements' => $evenements,
       ]);
    }
    
    
}
