<?php

namespace App\Controller;

use App\Entity\CreateProgramme;
use App\Form\CreateProgrammeType;
use App\Repository\CreateProgrammeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/create/programme")
 */
class CreateProgrammeController extends AbstractController
{
    /**
     * @Route("/", name="create_programme_index", methods={"GET"})
     */
    public function index(CreateProgrammeRepository $createProgrammeRepository): Response
    {
        return $this->render('create_programme/index.html.twig', [
            'create_programmes' => $createProgrammeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="create_programme_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $createProgramme = new CreateProgramme();
        $form = $this->createForm(CreateProgrammeType::class, $createProgramme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($createProgramme);
            $entityManager->flush();

            return $this->redirectToRoute('create_programme_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('create_programme/new.html.twig', [
            'create_programme' => $createProgramme,
            'form' => $form->createView(),
        ]);
    }
    

    /**
     * @Route("/{id}", name="create_programme_imprimer", methods={"GET"})
     */
    public function show(CreateProgramme $createProgramme): Response
    {
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($pdfOptions);
        $programmes= [];
        $programmes = $this->getDoctrine()->getRepository(CreateProgramme::class)->findAll();

        $html = $this->renderView('create_programme/show.html.twig',[ 'create_programme'=>$createProgramme]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();$dompdf->stream("mypdf.pdf", [
            "Attachment" => true
            
    
        ]);
        // Send some text response
        return new Response("The PDF file has been succesfully generated !");





        return $this->render('create_programme/show.html.twig', [
            'create_programme' => $createProgramme,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="create_programme_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, CreateProgramme $createProgramme, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CreateProgrammeType::class, $createProgramme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('create_programme_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('create_programme/edit.html.twig', [
            'create_programme' => $createProgramme,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="create_programme_delete", methods={"POST"})
     */
    public function delete(Request $request, CreateProgramme $createProgramme, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$createProgramme->getId(), $request->request->get('_token'))) {
            $entityManager->remove($createProgramme);
            $entityManager->flush();
        }

        return $this->redirectToRoute('create_programme_index', [], Response::HTTP_SEE_OTHER);
    }
}
