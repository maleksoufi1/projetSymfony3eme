<?php

namespace App\Controller;
use App\Entity\PropertySearch;
use App\Form\PropertySearchType;

use App\Entity\Programme;
use App\Entity\CategorieProgramme;
use App\Form\CategorieProgrammeType;
use App\Repository\CategorieProgrammeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/categorieProg")
 */
class CategorieProgrammeController extends AbstractController
{
    /**
    *@Route("/index",name="categorie_programme_index")
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
      $programmes = $this->getDoctrine()->getRepository(CategorieProgramme::class)->findAll();

     if($form->isSubmitted() && $form->isValid()) {
     //on récupère le nom d'article tapé dans le formulaire
      $nom = $propertySearch->getNom();
      if ($nom!="")
        //si on a fourni un nom d'article on affiche tous les articles ayant ce nom
       { $programmes = $this->getDoctrine()->getRepository(CategorieProgramme::class)->findBy(['libelle' => $nom] );

      }
      else  {
        //si si aucun nom n'est fourni on affiche tous les articles
        $programmes = $this->getDoctrine()->getRepository(CategorieProgramme::class)->findAll();
      }
     }
      return  $this->render('categorie_programme/index.html.twig',[ 'form' =>$form->createView(),'categorie_programmes'=>$programmes]);
    }








    
    /**
     * @Route("/front", name="categorie_programme_index2", methods={"GET"})
     */
    public function index2(CategorieProgrammeRepository $categorieProgrammeRepository): Response
    {
        return $this->render('categorie_programme/index2.html.twig', [
            'categorie_programmes' => $categorieProgrammeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="categorie_programme_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $categorieProgramme = new CategorieProgramme();
        $form = $this->createForm(CategorieProgrammeType::class, $categorieProgramme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form['image']->getData();
            $filename = md5(uniqid()).'.'.$uploadedFile->guessExtension();
            $uploadedFile->move($this->getParameter('upload_directory'),$filename);
            $categorieProgramme->setImage($filename);
            $categorieProgramme = $form->getData();
            $entityManager->persist($categorieProgramme);
            $entityManager->flush();

            return $this->redirectToRoute('categorie_programme_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('categorie_programme/new.html.twig', [
            'categorie_programme' => $categorieProgramme,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("show/{id}", name="categorie_programme_show", methods={"GET"})
     */
    public function show(CategorieProgramme $categorieProgramme): Response
    {
        return $this->render('categorie_programme/show.html.twig', [
            'categorie_programme' => $categorieProgramme,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="categorie_programme_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, CategorieProgramme $categorieProgramme, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategorieProgrammeType::class, $categorieProgramme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form['image']->getData();
            $filename = md5(uniqid()).'.'.$uploadedFile->guessExtension();
            $uploadedFile->move($this->getParameter('upload_directory'),$filename);
            $categorieProgramme->setImage($filename);
            $entityManager->flush();

            return $this->redirectToRoute('categorie_programme_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('categorie_programme/edit.html.twig', [
            'categorie_programme' => $categorieProgramme,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="categorie_programme_delete", methods={"POST"})
     */
    public function delete(Request $request, CategorieProgramme $categorieProgramme, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$categorieProgramme->getId(), $request->request->get('_token'))) {
            $entityManager->remove($categorieProgramme);
            $entityManager->flush();
        }

        return $this->redirectToRoute('categorie_programme_index', [], Response::HTTP_SEE_OTHER);
    }


   
}
