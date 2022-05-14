<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Billet;
use App\Form\BilletType;
use Symfony\Component\HttpFoundation\Request;
use Dompdf\Dompdf;
use Dompdf\Options;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


class BilletController extends AbstractController
{
    /**
     * @Route("/billet", name="billet")
    
     */
    public function index(): Response
    {
        return $this->render('billet/index.html.twig', [
            'controller_name' => 'BilletController',
        ]);
    }

    /**
    * @Route("/listBillet", name="listBillet")
    * @IsGranted("ROLE_ADMIN")
    */
public function list(): Response
{
   $rep=$this->getDoctrine()->getRepository(Billet::class);

   $billets =$rep-> findAll();

   return $this->render('billet/listbillet.html.twig', [
       'controller_name' => 'BilletController',
       'billets' => $billets,
   ]);

   
}
/**
* @Route("/listBilletpdf/{id}", name="listBilletpdf")
* @IsGranted("ROLE_CLIENT")
*/
public function listBilletpdf($id): Response
{
    $pdfOptions = new Options();
    
        $pdfOptions->set('defaultFont', 'Arial');
        
        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        $rep=$this->getDoctrine()->getRepository(Billet::class);
        $billets =$rep->find($id);
       
        
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('billet/pdf.html.twig', [
            'controller_name' => 'BilletController',
            'billets' => $billets,
        ]);
        
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
   
}

    /**
     * @Route("/addBillet", name="addBillet")
     * @IsGranted("ROLE_ADMIN")
     */
    public function addBillet(Request $request): Response
    {

        $billet = new Billet();
        $form = $this->createForm(BilletType::class , $billet);
        $form = $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $billet = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($billet);
            $em->flush();
            return $this->redirectToRoute('listBillet');
        }


        return $this->render('billet/addBillet.html.twig', [
            'formAddBillet'=>$form->createView(),
        ]);
    }



    /**
     * @Route("/updateBillet/{id}", name="updateBillet")
    
     */
    public function updateBillet(Request $request , $id): Response
    {
        $rep = $this->getDoctrine()->getRepository(Billet::class);
        $billet  = $rep->find($id);
        $form = $this->createForm(BilletType::class , $billet);
        $form = $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
          
             $em = $this->getDoctrine()->getManager();
             $em->flush();
             return $this->redirectToRoute('listBillet');
         }
 
        return $this->render('billet/updateBillet.html.twig', [
            'formUpdateBillet'=> $form->createView(),
     ]);
        
    }


    /**
     * @Route("/deleteBillet/{id}", name="deleteBillet")
   
     */
    public function deleteBillet($id): Response
    {
        $rep = $this->getDoctrine()->getRepository(Billet::class);
        $em = $this->getDoctrine()->getManager();
        $billet = $rep->find($id);
        $em->remove($billet);
        $em->flush();
        return $this->redirectToRoute('listBillet');
    }



/**
* @Route("/listBilletC/{id}", name="listBilletC")

*/
public function listBilletC(Request $request , $id, PaginatorInterface $paginator): Response
{
   $rep=$this->getDoctrine()->getRepository(Billet::class);

   $donnees =$rep-> findByIdEvenement($id);
   
   $billets = $paginator->paginate(
       $donnees,
       $request->query->getInt('page',1),
       3
   );
   return $this->render('billet/index.html.twig', [
       
       'billets' => $billets,
   ]);
}


    


}
