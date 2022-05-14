<?php

namespace App\Controller;

use App\Entity\CategorieProduit;
use App\Form\CategorieProduitType;
use App\Repository\CategorieProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/categorie/produit")
 */
class CategorieProduitController extends AbstractController
{/**
 * @Route("/searchProduit", name="searchProduit")
 */

    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $requestString = $request->get('q');
        $posts =  $em->getRepository(CategorieProduit::class)->findEntitiesByString($requestString);
        if(!$posts) {
            $result['posts']['error'] = "Post Not found 🙁 ";
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
            $realEntities[$posts->getId()] = [$posts-> getLibelle(),$posts->getDescription()];

        }
        return $realEntities;
    }


    /**
     * @Route("/", name="categorie_produit_index", methods={"GET"})
    *  @IsGranted("ROLE_ADMIN") 
     */
    public function index(CategorieProduitRepository $categorieProduitRepository): Response
    {
        return $this->render('categorie_produit/index.html.twig', [
            'categorie_produits' => $categorieProduitRepository->findAll(),


        ]);
    }
    /**
     * @Route("/index2", name="categorie_produit_index2", methods={"GET"})
     */
    public function index2(CategorieProduitRepository $categorieProduitRepository): Response
    {
        return $this->render('categorie_produit/index2.html.twig', [
            'categorie_produits' => $categorieProduitRepository->findAll(),


        ]);
    }

    /**
     * @Route("/new", name="categorie_produit_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $categorieProduit = new CategorieProduit();
        $form = $this->createForm(CategorieProduitType::class, $categorieProduit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form['image']->getData();
            $filename = md5(uniqid()).'.'.$uploadedFile->guessExtension();
            $uploadedFile->move($this->getParameter('upload_directory'),$filename);
            $categorieProduit->setImage($filename);


            $entityManager->persist($categorieProduit);
            $entityManager->flush();

            return $this->redirectToRoute('categorie_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('categorie_produit/new.html.twig', [
            'categorie_produit' => $categorieProduit,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="categorie_produit_show", methods={"GET"})
     */
    public function show(CategorieProduit $categorieProduit): Response
    {
        return $this->render('categorie_produit/show.html.twig', [
            'categorie_produit' => $categorieProduit,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="categorie_produit_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, CategorieProduit $categorieProduit, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategorieProduitType::class, $categorieProduit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form['image']->getData();
            $filename = md5(uniqid()).'.'.$uploadedFile->guessExtension();
            $uploadedFile->move($this->getParameter('upload_directory'),$filename);
            $categorieProduit->setImage($filename);


            $entityManager->flush();

            return $this->redirectToRoute('categorie_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('categorie_produit/edit.html.twig', [
            'categorie_produit' => $categorieProduit,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="categorie_produit_delete", methods={"POST"})
     */
    public function delete(Request $request, CategorieProduit $categorieProduit, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$categorieProduit->getId(), $request->request->get('_token'))) {
            $entityManager->remove($categorieProduit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('categorie_produit_index', [], Response::HTTP_SEE_OTHER);
    }






}
