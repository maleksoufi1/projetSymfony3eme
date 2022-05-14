<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\CategorieEvenement;
use App\Form\CatEvenementType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use App\Repository\CategorieEvenementRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class CategorieEvenementController extends AbstractController
{
    /**
     * @Route("/categorie/evenement", name="categorie_evenement")
     */
    public function index(): Response
    {
        return $this->render('categorie_evenement/index.html.twig', [
            'controller_name' => 'CategorieEvenementController',
        ]);
    }

    /**
* @Route("/listCategorie", name="listCategorie")
*  @IsGranted("ROLE_ADMIN")
*/
public function list(Request $request, PaginatorInterface $paginator): Response
{
   $rep=$this->getDoctrine()->getRepository(CategorieEvenement::class);

   $categories =$rep-> findAll();

   return $this->render('categorie_evenement/listCE.html.twig', [
       'controller_name' => 'CategorieEvenementController',
       'categories' => $categories,
   ]);
}
 /**
* @Route("/listCategorieF", name="listCategorieF")
*  @IsGranted("ROLE_ADMIN","ROLE_CLIENT")
*/
public function listF(Request $request, PaginatorInterface $paginator): Response
{
   $rep=$this->getDoctrine()->getRepository(CategorieEvenement::class);

   $donnees =$rep-> findAll();
   $categories = $paginator->paginate(
       $donnees,
       $request->query->getInt('page',1),
       3
   );

   return $this->render('categorie_evenement/index.html.twig', [
       'controller_name' => 'CategorieEvenementController',
       'categories' => $categories,
   ]);
}
/**
     * @Route("/addCatEvenement", name="addCatEvenement")
      *  @IsGranted("ROLE_ADMIN")
     */
    public function addCatEvenement(Request $request): Response
    {

        $categorie = new CategorieEvenement();
        $form = $this->createForm(CatEvenementType::class , $categorie);
        $form = $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $categorie = $form->getData();
            
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
            $categorie->setImage($fileName);
            $em->persist($categorie);
            $em->flush();
            
            return $this->redirectToRoute('listCategorie');
        }


        return $this->render('categorie_evenement/addCatEvenement.html.twig', [
            'formAddCatEvenement'=>$form->createView(),
        ]);
    }


    /**
     * @Route("/updateCatEvenement/{id}", name="updateCatEvenement")
     *  @IsGranted("ROLE_ADMIN")
     */
    public function updateCatEvenement(Request $request , $id): Response
    {
        $rep = $this->getDoctrine()->getRepository(CategorieEvenement::class);
        $categorie  = $rep->find($id);
        $form = $this->createForm(CatEvenementType::class , $categorie);
        $form = $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
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
             $categorie->setImage($fileName);
             $em->flush();
             return $this->redirectToRoute('listCategorie');
         }
 
        return $this->render('categorie_evenement/updateCatEvenement.html.twig', [
            'formUpdateCatEvenement'=> $form->createView(),
     ]);
        
    }


    /**
     * @Route("/deleteCatEvenement/{id}", name="deleteCatEvenement")
     *  @IsGranted("ROLE_ADMIN","ROLE_CLIENT")
     */
    public function deleteCatEvenement($id): Response
    {
        $rep = $this->getDoctrine()->getRepository(CategorieEvenement::class);
        $em = $this->getDoctrine()->getManager();
        $categorie = $rep->find($id);
        $em->remove($categorie);
        $em->flush();
        return $this->redirectToRoute('listCategorie');
    }
    

/**
     * @Route("/searchEvenement", name="searchEvenement")
     */
    public function searchEvenement(Request $request , CategorieEvenementRepository $catevenmentRepository){
        $em = $this->getDoctrine()->getManager();
        $requestString = $request->get('q');
        $posts =  $catevenmentRepository->findEntitiesByString($requestString);
        if(!$posts) {
            $result['posts']['error'] = "Pas de catégorie !  ";
        } else {
            $result['posts'] = $this->getRealEntities($posts);
        }
        return new Response(json_encode($result));
    }
    public function getRealEntities($posts){
        foreach ($posts as $posts){
            $realEntities[$posts->getId()] = [$posts->getImage(),$posts->getLibelle()];

        }
        return $realEntities;
    }

    
   
}




 
