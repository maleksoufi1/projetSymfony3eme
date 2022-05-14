<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\User;
use App\Repository\CommandeRepository;
use App\Repository\ProduitRepository;
use App\Repository\UserRepository;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;
use Doctrine\DBAL\Types\TextType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Produit;
use Symfony\Component\HttpFoundation\Request;
use App\Form\AddProduitType;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts;
use Swift_SmtpTransport;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Twilio\Rest\Client as Client;




class ProduitController extends AbstractController
{/**
 * @Route("/stat/{id}", name="stat")
 */
    public function statAction($id): Response
    {
        $pieChart = new PieChart();

        $entityManager = $this->getDoctrine()->getManager();
        $objet = $entityManager->getRepository(Produit::class)->find($id);
        $pieChart = new PieChart();
        $pieChart->getData()->setArrayToDataTable( array(
            ['produit', 'Nombre de jaime'],
            ['Jaime',     $objet->getJaime() ],
            ['Jaime pas',      $objet->getJaimepas() ],
        ));

        $pieChart->getOptions()->setTitle('Stat Jaime par produit');
        $pieChart->getOptions()->setHeight(400);
        $pieChart->getOptions()->setWidth(400);
        $pieChart->getOptions()->getTitleTextStyle()->setColor('#07600');
        $pieChart->getOptions()->getTitleTextStyle()->setFontSize(25);


        return $this->render('produit/statrec.html.twig', array(
                'piechart' => $pieChart,
            )

        );

    }
    /**
     * @Route("/produit", name="produit")
     */
    public function index(): Response
    {      $rep = $this->getDoctrine()->getRepository(produit::class);
        $produits = $rep->findAll();
        return $this->render('produit/index.html.twig', [
            'controller_name' => 'ProduitController',
            'produits'=>$produits
        ]);
    }

    /**
     * @Route("/passer/{id}", name="passer", methods={"GET","POST"})
     */
    public function passer(ProduitRepository $produitRepository,$id,CommandeRepository $commandeRepository,\Swift_Mailer $mailer,User $userr): Response
    {

        $entityManager = $this->getDoctrine()->getManager();
        $produit = $entityManager->getRepository(Produit::class)->find($id);
        $cmd = new Commande();
        $user = $entityManager->getRepository(User::class)->find($this->getUser());
        $cmd->setUser($user);
        $cmd->addProduit($produit);
        $cmd->setEtat("en_cours");
        $cmd->setPrixTotal($produit->getPrix());
        $entityManager->persist($cmd);
        $entityManager->flush();



        $message = (new \Swift_Message('Hello Email'))
            ->setFrom('noreplay.espritwork@gmail.com')
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                // templates/emails/registration.html.twig
                    'commande/registration.html.twig',
                    compact('cmd')
                ),
                'text/html'
            )
        ;
        $mailer->send($message);


        




        return $this->render('commande/request.html.twig');

    }

    /**
     * @Route("/listproduits", name="listproduits")
                *  @IsGranted("ROLE_ADMIN")
     */
    public function list(Request $request, PaginatorInterface $paginator): Response
    {
        $donnees = $this->getDoctrine()->getRepository(Produit::class)->findBy([],['prix' => 'desc']);
        // Paginate the results of the query
        $produits = $paginator->paginate(
        // Doctrine Query, not results
            $donnees,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            4
        );

        return $this->render('produit/listproduits.html.twig', [
            'produits'=>$produits,
        ]);

    }



    /**
     * @Route("/addproduit", name="addproduit")
     *  @IsGranted("ROLE_ADMIN","ROLE_CLIENT")
     */
    public function addProduit(Request $request): Response
    {

        $produit = new produit();
        $form = $this->createForm(AddProduitType::class , $produit);
        $form = $form->handleRequest($request);

        if ($form->isSubmitted()){
            $value=0;
            $produit->setJaime($value);
            $produit->setJaimepas($value);





            $uploadedFile = $form['image']->getData();
            $filename = md5(uniqid()).'.'.$uploadedFile->guessExtension();
            $uploadedFile->move($this->getParameter('upload_directory'),$filename);
            $produit->setImage($filename);

            $produit = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($produit);
            $em->flush();
            return $this->redirectToRoute('listproduits');
        }


        return $this->render('produit/addproduit.html.twig', [
            'formAddproduit'=>$form->createView(),
        ]);
    }


    /**
     * @Route("/updateproduit/{id}", name="updateproduit")
     *  @IsGranted("ROLE_ADMIN")
     */
    public function updateproduit(Request $request , $id): Response
    {
        $rep = $this->getDoctrine()->getRepository(produit::class);
        $produit  = $rep->find($id);
        $form = $this->createForm(AddProduitType::class , $produit);
        $form = $form->handleRequest($request);
        if ($form->isSubmitted()){
            $uploadedFile = $form['image']->getData();
            $filename = md5(uniqid()).'.'.$uploadedFile->guessExtension();
            $uploadedFile->move($this->getParameter('upload_directory'),$filename);
            $produit->setImage($filename);

            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('listproduits');
        }

        return $this->render('produit/updateproduit.html.twig', [
            'formUpdateproduit'=> $form->createView(),
        ]);

    }

    /**
     * @Route("/deleteproduit/{id}", name="deleteproduit")
     *  @IsGranted("ROLE_ADMIN")
     */
    public function deleteproduit($id): Response
    {
        $rep = $this->getDoctrine()->getRepository(Produit::class);
        $em = $this->getDoctrine()->getManager();
        $produit = $rep->find($id);
        $em->remove($produit);
        $em->flush();
        return $this->redirectToRoute('listproduits');
    }



    /**
     * @Route("/listProduitF/{id}", name="listProduitF", methods={"GET"})
     *  @IsGranted("ROLE_ADMIN","ROLE_CLIENT")
     */
    public function listProgrammeF(Request $request , $id): Response
    {
        $rep = $this->getDoctrine()->getRepository(Produit::class);
        $produits = $rep->findProgrammesByCat($id);

        return $this->render('produit/index.html.twig', [
            'produits'=>$produits,

        ]);

    }

    /**
     * @param $id
     * @param ProduitRepository $repository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/likeeventProduit/{id}", name="likeeventProduit")
     */
    public function likeEvent(ProduitRepository $repository , $id )
    {
        $produit=$repository->find($id);
        $new=$produit->getJaime() + 1;
        $produit->setJaime($new);
        $this->getDoctrine()->getManager()->flush();
        //return $this->render('home/afficheE.html.twig', ['event' => $event]);

        return $this->redirectToRoute('produit');
    }

    /**
     * @param $id
     * @param ProduitRepository $repository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/dislikeeventProduit/{id}", name="dislikeeventProduit")
     */
    public function dislikeEvent(ProduitRepository $repository , $id )
    {
        $produit=$repository->find($id);
        $new=$produit->getJaimepas() + 1;
        $produit->setJaimepas($new);
        $this->getDoctrine()->getManager()->flush();
        //return $this->render('home/afficheE.html.twig', ['event' => $event]);

        return $this->redirectToRoute('produit');
    }

















}
