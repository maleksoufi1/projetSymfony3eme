<?php

namespace App\Controller;

use App\Repository\CommandeRepository;
use App\Repository\OffreRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


use App\Entity\Commande;
use Symfony\Component\HttpFoundation\Request;
use App\Form\AddcommandeType;

class CommandeController extends AbstractController
{
    /**
     * @Route("/commande", name="commande")
     */
    public function index(): Response
    {
        return $this->render('commande/index.html.twig', [
            'controller_name' => 'CommandeController',
        ]);
    }


    /**
     * @Route("/listcommandes", name="listcommandes")
     */
    public function list(Request $request, PaginatorInterface $paginator): Response
    {


        $donnees = $this->getDoctrine()->getRepository(commande::class)->findBy([],['prixTotal' => 'desc']);
        // Paginate the results of the query
        $commandes = $paginator->paginate(
        // Doctrine Query, not results
            $donnees,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            4
        );

        return $this->render('commande/listcommandes.html.twig', [
            'commandes'=>$commandes,
        ]);

    }

    /**
     * @Route("/addcommande", name="addcommande")
     */
    public function addcommande(Request $request): Response
    {

        $commande = new commande();
        $form = $this->createForm(AddcommandeType::class , $commande);
        $form = $form->handleRequest($request);

        if ($form->isSubmitted()){
            $commande = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($commande);
            $em->flush();
            return $this->redirectToRoute('listcommandes');



        }


        return $this->render('commande/addcommande.html.twig', [
            'formAddcommande'=>$form->createView(),
        ]);
    }


    /**
     * @Route("/updatecommande/{id}", name="updatecommande")
     */
    public function updatecommande(Request $request , $id): Response
    {
        $rep = $this->getDoctrine()->getRepository(Commande::class);
        $commande  = $rep->find($id);
        $form = $this->createForm(AddcommandeType::class , $commande);
        $form = $form->handleRequest($request);
        if ($form->isSubmitted()){

            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('listcommandes');
        }

        return $this->render('commande/updatecommande.html.twig', [
            'formUpdatecommande'=> $form->createView(),
        ]);

    }


    /**
     * @Route("/deletecommande/{id}", name="deletecommande")
     */
    public function deletecommande($id): Response
    {
        $rep = $this->getDoctrine()->getRepository(Commande::class);
        $em = $this->getDoctrine()->getManager();
        $commande = $rep->find($id);
        $em->remove($commande);
        $em->flush();
        return $this->redirectToRoute('listcommandes');
    }
    /**
     * @Route("/student/listo", name="listo", methods={"GET"})
     */
    public function listo(CommandeRepository $commandeRepository): Response
    {
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('commande/listo.html.twig', [
            'commandes' => $commandeRepository->findAll(),
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        $dompdf->set_option('isRemoteEnabled', true);
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();
        // Store PDF Binary Data
        $output = $dompdf->output();

        // In this case, we want to write the file in the public directory
        $publicDirectory = $this->getParameter('upload_directory');
        // e.g /var/www/project/public/mypdf.pdf
        $pdfFilepath =  $publicDirectory . '/mypdf.pdf';

        // Write file to the desired path
        file_put_contents($pdfFilepath, $output);

        // Send some text response

        // Output the generated PDF to Browser (inline view)
        $dompdf->stream("mypdf.pdf", [
            "Attachment" => false
        ]);
    }








}
