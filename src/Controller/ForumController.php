<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Forum;
use App\Entity\Commentaire;
use App\Form\ForumType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class ForumController extends AbstractController
{
    /**
     * @Route("/forum", name="forum")
     *  @IsGranted("ROLE_ADMIN") 
     */
    public function index(Request $request,PaginatorInterface $paginator): Response
    {
        ///ajout de forum!!
        $forum = new Forum();
        $form=$this->createForm(ForumType::class,$forum);
        $form->handleRequest($request);
        
        
        if($form->isSubmitted()&& $form->isValid()){
            $forum = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($forum);
            $em->flush();
            return $this->redirectToRoute('forum');

    }
       //////////liste forum !!!
        $repository=$this->getDoctrine()->getRepository(Forum::class);
        $forum=$repository-> findAll();

        ///////////////////liste commentaire 
        $repository=$this->getDoctrine()->getRepository(Commentaire::class);
        $donnees=$repository-> findAll();
         $commentaire=$paginator->paginate(
        $donnees, // Requête contenant les données à paginer (ici nos articles)
        $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
        5);        // Nombre de résultats par page
        return $this->render('forum/index-admin.html.twig', [
            'formA' => $form->createView(),  'forums' => $forum,'cmnt'=>$commentaire,
        ]);
    }

      /**
     * @Route("/listForum", name="listForum")
     *  @IsGranted("ROLE_ADMIN") 
     */
    public function list(): Response
    {
        $repository=$this->getDoctrine()->getRepository(Forum::class);
        $forum=$repository-> findAll();
        return $this->render('commentaire/index.html.twig', [
            'forums' => $forum,
        ]);
    }
    
    
     /**
     * @Route("/ajouter_forum", name="AjouterForum")
   *  @IsGranted("ROLE_ADMIN") 
     */
    public function Ajouter_Forum(Request $request): Response
    
    {
        $forum = new Forum();
        $form=$this->createForm(ForumType::class,$forum);
        $form->handleRequest($request);
        
        
        if($form->isSubmitted()&& $form->isValid()){
            $forum = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($forum);
            $em->flush();
            return $this->redirectToRoute('listForum');

    }
        
        
        return $this->render('forum/index.html.twig', [
            'formA' => $form->createView()
        ]);
    }

      /**
     * @Route("/delete_forum/{id}", name="deleteForum")
   *  @IsGranted("ROLE_ADMIN") 
     */
    public function delete($id): Response
    { 
        $rep=$this->getDoctrine()->getRepository(Forum::class);
      $em=$this->getDoctrine()->getManager();
      $forum=$rep->find($id);
      $em->remove($forum);
      $em->flush();

        return $this->redirectToRoute('forum');
       
    }
          /**
     * @Route("/admin_delete_commentaire/{id}", name="AdeleteCommentaire")
     *  @IsGranted("ROLE_ADMIN") 
     */
    public function deleteC($id): Response
    { 
        $rep=$this->getDoctrine()->getRepository(Commentaire::class);
      $em=$this->getDoctrine()->getManager();
      $cmnt=$rep->find($id);
      $em->remove($cmnt);
      $em->flush();

        return $this->redirectToRoute('forum');
       
    }

    /**
     * @Route("/modifierForum/{id}", name="modifierForum")
     *  @IsGranted("ROLE_ADMIN") 
     */
    public function modifier(Request $request, $id): Response
    {
        $rep=$this->getDoctrine()->getRepository(Forum::class);
        $Forum = $rep->find($id);
        $form=$this->createForm(ForumType::class,$Forum);
        $form->handleRequest($request);
        if($form->isSubmitted()&& $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('forum');

        }

        return $this->render('forum/update.html.twig', [
            'formA' => $form->createView()
        ]);
    }

    
}
