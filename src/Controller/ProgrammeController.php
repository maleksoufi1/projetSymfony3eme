<?php

namespace App\Controller;

use App\Entity\PropertySearch;
use App\Entity\User;
use App\Form\PropertySearchType;
use App\Form\CategorySearchType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use App\Entity\Programme;
use Symfony\Component\HttpFoundation\Request;
use App\Form\ProgrammeType;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Controller\ProgrammeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class ProgrammeController extends AbstractController
{


    /**
    *@Route("/listProgramme",name="listProgramme")
     *  @IsGranted("ROLE_ADMIN") 
    */
  public function home(Request $request)
  {
    $propertySearch = new PropertySearch();
    $form = $this->createForm(PropertySearchType::class,$propertySearch);
    $form->handleRequest($request);
   //initialement le tableau des articles est vide, 
   //c.a.d on affiche les articles que lorsque l'utilisateur clique sur le bouton rechercher
    $programmes= [];
    $programmes = $this->getDoctrine()->getRepository(Programme::class)->findAll();
    
   if($form->isSubmitted() && $form->isValid()) {
   //on récupère le nom d'article tapé dans le formulaire
    $nom = $propertySearch->getNom();   
    if ($nom!="") 
      //si on a fourni un nom d'article on affiche tous les articles ayant ce nom
     { $programmes = $this->getDoctrine()->getRepository(Programme::class)->findBy(['titre' => $nom] );
        
    }
    else  { 
      //si si aucun nom n'est fourni on affiche tous les articles
      $programmes = $this->getDoctrine()->getRepository(Programme::class)->findAll();
    }
   }
    return  $this->render('programme/listProgrammes.html.twig',[ 'form' =>$form->createView(),'programmes'=>$programmes]);  
  }

    
      /**
    *@Route("/listP",name="listP")
    */
  public function home2()
  {
    $pdfOptions = new Options();
    $pdfOptions->set('defaultFont', 'Arial');
    
    // Instantiate Dompdf with our options
    $dompdf = new Dompdf($pdfOptions);
    $programmes= [];
     $programmes = $this->getDoctrine()->getRepository(Programme::class)->findAll();
    // Retrieve the HTML generated in our twig file
    $html = $this->renderView('programme/listp.html.twig',[ 'programmes'=>$programmes]);
    
    // Load HTML to Dompdf
    $dompdf->loadHtml($html);
    
    // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
    $dompdf->setPaper('A4', 'portrait');

    // Render the HTML as PDF
    $dompdf->render();

    // Output the generated PDF to Browser (force download)
    $dompdf->stream("mypdf.pdf", [
        "Attachment" => true

    ]);
    // Send some text response
    return new Response("The PDF file has been succesfully generated !");
}
     

    /**
     * @Route("/programme", name="programme")
     */
    public function index(): Response
    {
        return $this->render('programme/index.html.twig', [
            'controller_name' => 'ProgrammeController',
        ]);
    }

    /**
     * @Route("/listProgrammeALL", name="listProgrammeALL", methods={"GET"})
     */
    public function listProgrammeALL(Request $request , PaginatorInterface $paginator): Response
    {
        $rep = $this->getDoctrine()->getRepository(Programme::class);
        $programmes = $rep->findAll();
        $articles = $paginator->paginate(
            $programmes,
            $request->query->getInt('page',1),
            3              
        );
 
        return $this->render('programme/index.html.twig', [
          'programmes'=>$articles,
          
         
     ]);
        
    }


   
    /**
     * @Route("/listProgrammeF/{id}", name="listProgrammeF", methods={"GET"})
     */
    public function listProgrammeF(Request $request , $id, PaginatorInterface $paginator): Response
    {
        $rep = $this->getDoctrine()->getRepository(Programme::class);
        $programmes = $rep->findProgrammesByCat($id);
        $articles = $paginator->paginate(
            $programmes,
            $request->query->getInt('page',1),
            3              
        );
 
        return $this->render('programme/index.html.twig', [
          'programmes'=>$articles,
          
         
     ]);
        
    }

    


    /**
     * @Route("/addProgramme", name="addProgramme")
     */
    public function addProgramme(Request $request): Response
    {

        $programme = new programme();
        $form = $this->createForm(ProgrammeType::class , $programme);
        $form = $form->handleRequest($request);
                  $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->find($this->getUser());

        if ($form->isSubmitted()){
            $uploadedFile = $form['image']->getData();
            $filename = md5(uniqid()).'.'.$uploadedFile->guessExtension();
            $uploadedFile->move($this->getParameter('upload_directory'),$filename);
            $programme->setImage($filename);
            $programme = $form->getData();
            $programme->setUser($user);


            $em = $this->getDoctrine()->getManager();
            $em->persist($programme);
            $em->flush();
            return $this->redirectToRoute('listProgramme');
        }


        return $this->render('programme/addProgramme.html.twig', [
            'formAddProgramme'=>$form->createView(),
        ]);
    }


      /**
     * @Route("/updateProgramme/{id}", name="updateProgramme")
     */
    public function updateProgramme(Request $request , $id): Response
    {
        $rep = $this->getDoctrine()->getRepository(Programme::class);
        $programme  = $rep->find($id);
        $form = $this->createForm(ProgrammeType::class , $programme);
        $form = $form->handleRequest($request);
        if ($form->isSubmitted()){
            $uploadedFile = $form['image']->getData();
            $filename = md5(uniqid()).'.'.$uploadedFile->guessExtension();
            $uploadedFile->move($this->getParameter('upload_directory'),$filename);
            $programme->setImage($filename);
          
             $em = $this->getDoctrine()->getManager();
             $em->flush();
             return $this->redirectToRoute('listProgramme');
         }
 
        return $this->render('programme/updateProgramme.html.twig', [
            'formUpdateProgramme'=> $form->createView(),
     ]);
        
    }


    
     /**
     * @Route("/deleteProgramme/{id}", name="deleteProgramme")
     */
    public function deleteProgramme($id): Response
    {
        $rep = $this->getDoctrine()->getRepository(Programme::class);
        $em = $this->getDoctrine()->getManager();
        $programme = $rep->find($id);
        $em->remove($programme);
        $em->flush();
        return $this->redirectToRoute('listProgramme');
    }

    /**
     * @Route("/searchProgramme", name="searchProgramme")
     */

    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $requestString = $request->get('q');
        $posts =  $em->getRepository(Programme::class)->findEntitiesByString($requestString);
        if(!$posts) {
            $result['posts']['error'] = "Post Not found :( ";
        } else {
            $result['posts'] = $this->getRealEntities($posts);
        }
        return new Response(json_encode($result));
    }

    /**
     * @Route("/getRealEntities", name="getRealEntities")
     */
    public function getRealEntities($posts){
        foreach ($posts as $posts){
            $realEntities[$posts->getId()] = [$posts-> getTitre(),$posts->getImage(),$posts->getDifficulte(),$posts->getDuree(),$posts->getAffiche(),$posts->getType(),$posts->getDescription()];

        }
        return $realEntities;
    }

    /**
     * @Route("/programme_show/{id}", name="prgramme_show")
     */
    public function show2(Programme $Programme): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        
        $value = $Programme->getAbn();
        $value = $value + 1 ;
        $Programme->setAbn($value);
        $entityManager->flush();
        return $this->render('programme/show.html.twig', [
            'programmes' => $Programme,
        ]);
    }

     /**
     * @Route("/programme_showF/{id}", name="programme_show1")
     */
    public function show1(Programme $Programme): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        
        $value = $Programme->getAbn();
        $value = $value + 1 ;
        $Programme->setAbn($value);
        $entityManager->flush();
        return $this->render('programme/show2.html.twig', [
            'programmes' => $Programme,
        ]);
    }

    /**
     * @param $id
     * @param ProgrammeRepository $repository;
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/likeevent/{id}", name="likeevent")
     */
    public function likeEvent( $id )
    {
       $rep = $this->getDoctrine()->getRepository(Programme::class);
        $em = $this->getDoctrine()->getManager();
        $programme=$rep->find($id);
        $new=$programme->getJaime() + 1;
        $programme->setJaime($new);
        $this->getDoctrine()->getManager()->flush();
        //return $this->render('home/afficheE.html.twig', ['event' => $event]);

        return $this->redirectToRoute('programme');
    }

    /**
     * @param $id
     * @param ProgrammeRepository $repository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/dislikeevent/{id}", name="dislikeevent")
     */
    public function dislikeEvent($id )
    {
        $rep = $this->getDoctrine()->getRepository(Programme::class);
        $em = $this->getDoctrine()->getManager();
        $programme=$rep->find($id);
        $new=$programme->getJaimepas() + 1;
        $programme->setJaimepas($new);
        $this->getDoctrine()->getManager()->flush();
        //return $this->render('home/afficheE.html.twig', ['event' => $event]);

        return $this->redirectToRoute('programme');
    }
    
    



}
