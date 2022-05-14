<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Commentaire;
use App\Entity\Forum;
use App\Form\CommentaireType;
use Knp\Component\Pager\PaginatorInterface;
use App\Form\UpdateFormType;
use Symfony\Component\Security\Core\Security;
class CommentaireController extends AbstractController
{
     
    /**
     * @Route("/commentaire", name="commentaire")
     */
    public function index(Request $request , PaginatorInterface $paginator ): Response
    {
        
        
         //// ajouter cmnt
         $cmnt = new Commentaire();
         $form=$this->createForm(CommentaireType::class,$cmnt);
        $form->handleRequest($request);
         if($form->isSubmitted()&& $form->isValid())
        {
           
           $cmnt = $form->getData();
           
           $cmnt->setTemps(new \DateTime());
           $cmnt->setVus(0);
           
           $em = $this->getDoctrine()->getManager();
           $em->persist($cmnt);
           $em->flush();
           return $this->redirectToRoute('commentaire');

   }
         
       
         //$mots[]=array("bonjour","hello","good morning","sallem","coucou");
         
      
            
          /*  vérifier le contenu avec la méthode de tableau
          for($i=1;$i<sizeof($mots);$i++)
            {
             
                if(strpos($cmnt->getContenu(),$mots[$i])>0)
                {
                    return $this->render('commentaire/add.html.twig', [
                        'controller_name' => 'CommentaireController', 'buf'=>$mots[$i],
                    ]);
                }
                

            }*/

         /*   
          vérifier le contenu avec la méthode de fichiers.
         $handle = fopen(__DIR__ .'/commentaire.txt','r');
            
                while (!feof($handle)) 
                {
                    $buffer = fgets($handlme);
                    
                 if(str_contains($cmnt->getContenu(),$buffer))
                {
                    fclose($handle);
                     
                    return $this->render('commentaire/add.html.twig', [
                        'controller_name' => 'CommentaireController', 'buf'=>$buffer,
                    ]);
                }
               
               
              

            }
            fclose($handle);*/
             
           

            
 
     


     ///// lister cmnt

     
     $repository=$this->getDoctrine()->getRepository(Commentaire::class);
     $donnees=$repository-> findAll();
     $Commentaire=$paginator->paginate(
        $donnees, // Requête contenant les données à paginer (ici nos articles)
        $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
        5);        // Nombre de résultats par page
    
      
         
      //////afficher forum
      $repository=$this->getDoctrine()->getRepository(Forum::class);
      $forum=$repository-> findAll();
    
        

        
        return $this->render('commentaire/index.html.twig', [
            'controller_name' => 'CommentaireController', 'cmnt' => $Commentaire,'forums' => $forum,'formA' => $form->createView(),
        ]);


       
        
    }
    /**
     * @Route("/afficher/commentaire_forum/{id}", name="afficherCF")
     */
    public function afficher_commentaire_forum(Request $request,$id,PaginatorInterface $paginator): Response
    {
        $cmnt = new Commentaire();
        $form=$this->createForm(CommentaireType::class,$cmnt);
        $form->handleRequest($request);
        //$mots[]=array("bonjour","hello","good morning","sallem","coucou");
        $rep=$this->getDoctrine()->getRepository(Forum::class);
        $em=$this->getDoctrine()->getManager();
        $frm=$rep->find($id);
        $cmnt->setForum($frm);
        $user=$this->get('security.token_storage')->getToken()->getUser();
           $cmnt->setUser($user);
        if($form->isSubmitted()&& $form->isValid())
        {
           
           $cmnt = $form->getData();
           
           $cmnt->setTemps(new \DateTime());
           $cmnt->setVus(0);
           
           $em = $this->getDoctrine()->getManager();
           $em->persist($cmnt);
           $em->flush();
           return $this->redirectToRoute('commentaire');

   }

    ////////afficher les commentaires par forum

    $repository=$this->getDoctrine()->getRepository(Commentaire::class);
    $donnees= $repository->findByForum($id);    
    $comentaire=$paginator->paginate(
        $donnees, // Requête contenant les données à paginer (ici nos articles)
        $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
        5);        // Nombre de résultats par page
   

    ///////affiche commentaire 
    $repository=$this->getDoctrine()->getRepository(Forum::class);
    $forum=$repository-> findAll();

    return $this->render('commentaire/index1.html.twig', [
        'controller_name' => 'CommentaireController',  'cmnt' => $comentaire,'forums' => $forum,'formA' => $form->createView(),'id_forum'=>$id,
    ]);


    }
     
    
    /**
     * @Route("/Rechercher", name="Rechercher")
     */
    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $requestString = $request->get('q');
        $posts =  $em->getRepository(Commentaire::class)->findEntitiesByString($requestString);
        if(!$posts) {
            $result['posts']['error'] = "Post Not found :( ";
        } else {
            $result['posts'] = $this->getRealEntities($posts);
        }
        return new Response(json_encode($result));
    }

    public function getRealEntities($posts){
        foreach ($posts as $posts){
            $realEntities[$posts->getId()] = [$posts->getContenu(),$posts->getType()];

        }
        return $realEntities;
    }

       /**
     * @Route("/listCommentaire", name="listCommentaire")
     */
    public function list(): Response
    {
        $repository=$this->getDoctrine()->getRepository(Commentaire::class);
        $Commentaire=$repository-> findAll();
        return $this->render('commentaire/list.html.twig', [
            'cmnt' => $Commentaire,
        ]);
    }

     /**
     * @Route("/ajouter_commentaire", name="AjouterCommentaire")
     */
    public function Ajouter_Commentaire(Request $request): Response
    
    {
        $cmnt = new Commentaire();
        $form=$this->createForm(CommentaireType::class,$cmnt);
        $form->handleRequest($request);
        
        
        if($form->isSubmitted()&& $form->isValid()){
            
            $cmnt = $form->getData();
        
            $em = $this->getDoctrine()->getManager();
            $em->persist($cmnt);
            $em->flush();
            return $this->redirectToRoute('listCommentaire');

    }
        
        
        return $this->render('commentaire/add.html.twig', [
            'formA' => $form->createView()
        ]);
    }

      /**
     * @Route("/delete_commentaire/{id}", name="deleteCommentaire")
     */
    public function delete($id): Response
    { 
        $rep=$this->getDoctrine()->getRepository(Commentaire::class);
      $em=$this->getDoctrine()->getManager();
      $cmnt=$rep->find($id);
      $em->remove($cmnt);
      $em->flush();

        return $this->redirectToRoute('commentaire');
       
    }

    /**
     * @Route("/modifierCommentaire{id}", name="modifierCommentaire")
     */
    public function modifier(Request $request, $id): Response
    {
        $rep=$this->getDoctrine()->getRepository(Commentaire::class);
        $Commentaire= $rep->find($id);
        $form=$this->createForm(UpdateFormType::class,$Commentaire);
        $form->handleRequest($request);
        if($form->isSubmitted()&& $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('commentaire');

        }

        return $this->render('commentaire/update.html.twig', [
            'formA' => $form->createView()
        ]);
    }
    /**
     * @Route("/add_views/{id}", name="add_views")
     */
    public function views(Request $request, $id): Response
    {
        $rep=$this->getDoctrine()->getRepository(Commentaire::class);
        $Commentaire= $rep->find($id);
        $Commentaire->setVus($Commentaire->getVus()+1);
        
        
            $em = $this->getDoctrine()->getManager();
            $em->persist($Commentaire);
            $em->flush();
            return $this->redirectToRoute('commentaire');

       

        return $this->render('commentaire/update.html.twig', );
    }
   
}
