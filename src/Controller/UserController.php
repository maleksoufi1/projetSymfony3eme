<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Validator\Constraints\Json;
use App\Security\UsersAuthenticator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Symfony\Component\Security\Core\Encoder\AuthenticatorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Security\LoginFormAuthentificatorAuthenticator;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use App\Form\RegistrationFormType;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface; 
use App\Form\UserType;
use App\Form\AjouterUserType;
use App\Form\ModifierUserType;
use App\Repository\UserRepository;
use Symfony\Component\Serializer\Annotation\Groups;

use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
/// produit 
use App\Entity\Commande;
 
use App\Repository\CommandeRepository;
use App\Repository\ProduitRepository;
 
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;
use Doctrine\DBAL\Types\TextType;
use Knp\Component\Pager\PaginatorInterface;
 
 
use App\Entity\Produit;
 
use App\Form\AddProduitType;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts;
use Swift_SmtpTransport;
use Swift_Mailer;
use Swift_Message;
use Twilio\Rest\Client as Client;
 
 
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;


//programme 
use App\Entity\PropertySearch;
use App\Form\PropertySearchType;

use App\Entity\Programme;
use App\Entity\CategorieProgramme;
use App\Form\CategorieProgrammeType;
use App\Repository\CategorieProgrammeRepository;
 
 
use App\Form\CategorySearchType;
 
 
 
 
 


 
use Dompdf\Dompdf;
 
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Controller\ProgrammeRepository;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
 
 

 
 
 
class UserController extends AbstractController
{
    /**
     * @Route("/user", name="app_user")
     */
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
            
  /**
     * @Route("/ajouterUser", name="ajouterUser")
     * @Method("POST")
     */
    public function ajouter(Request $request  , UserPasswordEncoderInterface $userPasswordEncoder,SerializerInterface $serializerInterface )
    {
        $user = new User();
        $nom = $request->query->get("nom");
        $prenom = $request->query->get("prenom");
        //$poids = $request->query->get("poids");
        //$taille = $request->query->get("taille");
        $email = $request->query->get("email");
        $role = $request->query->get("role");
        $Password = $request->query->get("Password");
        if (!filter_var($email,FILTER_VALIDATE_EMAIL)) {
            return new response ("email invalid .");
            }
   
        // $sexe = $request->query->get("sexe");
            $user->setPassword(
                $userPasswordEncoder->encodePassword(
                        $user,
                        $Password
                    )
                );
            
            $user->setNom($nom);
            $user->setEmail($email);
            $user->setPrenom($prenom);
            // $user->setPoids($poids);
            // $user->setTaille($taille);
            // $user->setSexe($sexe);
            $user->setRole($role);
           
            $user->setActivationToken(md5(uniqid()));

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
           $serializer = new Serializer([new ObjectNormalizer()]);
          $formatted = $serializer->normalize("utilisateur ajoute avec succes.");
          return new JsonResponse($formatted);

       
}

  /**
     * @Route("/supprimerUser/{id}", name="supprimerUser")
     */
    public function deleteUser($id) {
        $em= $this->getDoctrine()->getManager();
        $user = $em->getRepository(user::class)->find($id);
        $em->remove($user);
        $em->flush();
        return new JsonResponse("User deleted .");

    }
   

   
        /**
      * @Route("/afficherUser", name="afficherUser")
      */
      public function afficherUser()
      {
 
         
        $p = $this->getDoctrine()->getManager()->getRepository(User::class)->findAll();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($p);


             return new JsonResponse($formatted);
      }
    /**
      * @Route("/loginM", name="loginM")
      */
      public function loginM(Request $request)
      {
        $email = $request->query->get("email");
        $password = $request->query->get("password");
        $em=$this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->findOneBy(['email'=>$email]);
        if($user){
            if(password_verify($password,$user->getPassword())){
                $serializer = new Serializer([new ObjectNormalizer()]);
                $formatted = $serializer->normalize($user);

                return new JsonResponse($formatted);
            }else{ 
                return new response ("password not found .");


            }
        }else{

            return new response ("user not found .");
        }
      }

      /**
      * @Route("/inscriptionUser", name="inscriptionUser")
      */
      public function singnupAction(Request $request, UserPasswordEncoderInterface $userPasswordEncoder ,EntityManagerInterface $entityManager, GuardAuthenticatorHandler $guardHandler, LoginFormAuthentificatorAuthenticator $authenticator,MailerInterface $mailer): Response
      {
        $email = $request->query->get("email");
        $password = $request->query->get("password");
        $nom = $request->query->get("nom");
        $prenom = $request->query->get("prenom");
        // $poids = $request->query->get("poids");
       //  $taille = $request->query->get("taille");
        // $sexe = $request->query->get("sexe");
        if (!filter_var($email,FILTER_VALIDATE_EMAIL)) {
        return new response ("email invalid .");
        }
        $user = new User();
        $user->setEmail($email);
        
        $user->setPassword(
            $userPasswordEncoder->encodePassword(
                    $user,$password)
            );
    $user->setNom($nom);
    $user->setPrenom($prenom);
    // $user->setPoids($poids);
    // $user->setTaille($taille);
    // $user->setSexe($sexe);
    $user-> setRole("client");
    $user->setRoles(["ROLE_CLIENT"]);
    $user->setActivationToken(md5(uniqid()));
    try {
        $em = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();
        // do anything else you need here, like send an email
    // On crée le message
    $message =(new TemplatedEmail())
    // On attribue l'expéditeur
    ->from(new Address('adomifit.123@gmail.com', 'A Domifit'))
    // On attribue le destinataire
   
    
    ->to($user->getEmail())
    ->subject('Activation compte')
    // On crée le texte avec la vue
    ->htmlTemplate('emails/activation.html.twig')
    ->context([
        'token' => $user->getActivationToken(),
    ])
;
    $mailer->send($message);
            // do anything else y   ou need here, like send an email

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        
        return new JsonResponse ("creation compte avec succes",200);
    } catch(\Exception $ex)
    {
        return new JsonResponse ("Excption".$ex->getMessage());
    }
      }
 /**
      * @Route("/user/editUser", name="app_gestion_profil")
      */
      public function editUser(Request $request,UserPasswordEncoderInterface $userPasswordEncoder)
      {
        $id = $request->get("id");
        $email = $request->query->get("email");
        $nom = $request->query->get("nom");
        $prenom = $request->query->get("prenom");  
        $em = $this->getDoctrine()->getManager();
        $user= $em->getRepository(User::class)->find($id);
      
 if ($request->files->get("photo")!=null){
        $file =$request->files->get("photo");
        $fileName= $file->getClientOriginalName();
        $file->move($fileName);
 }
        $user->setEmail($email);
        $user->setNom($nom);
        $user->setPrenom($prenom);
      

            try {
                $em = $this->getDoctrine()->getManager();
                
                $em->flush();
           
                return new JsonResponse (" modification avec succes",200);
            } catch(\Exception $ex)
            {
                return new JsonResponse ("Excption".$ex->getMessage());
            }

      }
       
// PRODUIT  -------------------------------------------------------------------------
/**
     * @param $id
     * @param ProduitRepository $repository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/likeevent/{id}", name="likeevent")
     */
    public function likeEvent2(ProduitRepository $repository , $id )
    {
        $produit=$repository->find($id);
        $new=$produit->getJaime() + 1;
        $produit->setJaime($new);
        $this->getDoctrine()->getManager()->flush();
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(1);
// Add Circular reference handler
        $normalizer->setCircularReferenceHandler(function ($object) {

        });
        $rep = $this->getDoctrine()->getRepository(Produit::class);
        $programmes = $rep->findAll();
        $normalizers = array($normalizer);
        $serializer = new Serializer($normalizers, $programmes);
        $formatted = $serializer->normalize($programmes);

        // return new JsonResponse("Reclamation a ete modifiee avec success.");
        return new JsonResponse($formatted);
    }


    /**
     * @param $id
     * @param ProduitRepository $repository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/dislikeevent/{id}", name="dislikeevent")
     */
    public function dislikeEvent2(ProduitRepository $repository , $id )
    {
        $produit=$repository->find($id);
        $new=$produit->getJaimepas() + 1;
        $produit->setJaimepas($new);
        $this->getDoctrine()->getManager()->flush();
        //return $this->render('home/afficheE.html.twig', ['event' => $event]);

        //return $this->redirectToRoute('produit');
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(1);
// Add Circular reference handler
        $normalizer->setCircularReferenceHandler(function ($object) {

        });
        $rep = $this->getDoctrine()->getRepository(Produit::class);
        $programmes = $rep->findAll();
        $normalizers = array($normalizer);
        $serializer = new Serializer($normalizers, $programmes);
        $formatted = $serializer->normalize($programmes);

        // return new JsonResponse("Reclamation a ete modifiee avec success.");
        return new JsonResponse($formatted);
    }











    // Les fonctions Api
    /**
     * @Route("/AllProduit", name="AllProduit")
     */
    public function AllProduit(NormalizerInterface $normalizer){

        $rep = $this->getDoctrine()->getRepository(Produit::class);
        $Produit = $rep->findAll();

        $json = $normalizer->normalize($Produit , 'json' , ['groups'=>'ProduitR']);

        return new Response(json_encode($json));
    }


    /******************Ajouter CatProduit*****************************************/
    /**
     * @Route("/addProduitRs", name="addProduitR")
     * methods={"POST"}
     */

    public function addProduitR(Request $request)
    {
        $catRegime = new Produit();
        $libelle = $request->query->get("nom");
        $description = $request->query->get("description");
        $image = $request->query->get("image");

        $em = $this->getDoctrine()->getManager();


        $catRegime->setNom($libelle);
        $catRegime->setDescription($description);
        $catRegime->setImage($image);



        $em->persist($catRegime);
        $em->flush();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($catRegime);
        return new JsonResponse($formatted);

    }
    /******************Supprimer CatRegime*****************************************/

    /**
     * @Route("/deleteProdR", name="deleteProdR")
     * methods=("DELETE")
     */

    public function deleteCatR(Request $request) {
        $id = $request->get("id");
        $em = $this->getDoctrine()->getManager();
        $catRegime = $em->getRepository(Produit::class)->find($id);
        if($catRegime!=null ) {
            $em->remove($catRegime);
            $em->flush();

            $serialize = new Serializer([new ObjectNormalizer()]);
            $formatted = $serialize->normalize("Produit a ete supprimee avec success.");
            return new JsonResponse($formatted);

        }
        return new JsonResponse("id reclamation invalide.");


    }

    /******************Modifier CategorieR*****************************************/
    /**
     * @Route("/updateProdR", name="updateProdR")
     * Method("PUT")
     */
    public function updateCategorieR(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $Catregime = $this->getDoctrine()->getManager()
            ->getRepository(Produit::class)
            ->find($request->get("id"));

        $Catregime->setNom($request->get("nom"));
        $Catregime->setDescription($request->get("description"));
        $Catregime->setImage($request->get("image"));



        $em->persist($Catregime);
        $em->flush();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($Catregime);
        return new JsonResponse("Reclamation a ete modifiee avec success.");

    }


// Programme -------------------------------------------------------------







/**
     * @Route("/listProgrammeALL", name="listProgrammeALL", methods={"GET"})
     */
    public function listProgrammeALL(Request $request , PaginatorInterface $paginator): Response
    {
        $rep = $this->getDoctrine()->getRepository(Programme::class);
        $programmes = $rep->findAll();
       
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(1);
// Add Circular reference handler
        $normalizer->setCircularReferenceHandler(function ($object) {
        
});
        $normalizers = array($normalizer);
        $serializer = new Serializer($normalizers, $programmes);
        $formatted = $serializer->normalize($programmes);

         return new JsonResponse($formatted);
        
    }


/**
     * @Route("/listProgrammeF/{id}", name="listProgrammeF", methods={"GET"})
     */
    public function listProgrammeF(Request $request , $id): Response
    {
       
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(1);
// Add Circular reference handler
        $normalizer->setCircularReferenceHandler(function ($object) {
        
    }); 
        $rep = $this->getDoctrine()->getRepository(Programme::class);
        
        $programmes= $rep->findProgrammesByCat($id);
       // $programmes= $rep->findAll();
        

        $normalizers = array($normalizer);
        $serializer = new Serializer($normalizers, $programmes);
        $formatted = $serializer->normalize($programmes);
       
       // return new JsonResponse("Reclamation a ete modifiee avec success.");
        return new JsonResponse($formatted);
            

      
       
    }



 
    /**
     * @Route("/addProgramme", name="addProgramme")
     */
    public function addProgramme(Request $request): Response
    {

        $programme= new Programme();
        $em = $this->getDoctrine()->getManager();
        $id= $request->query->get("categorie_programme_id");
       // $categorieprogramme = $em->getRepository(CategorieProgramme::class)->find($id);
        //$cat=$categorieprogramme->getId();
        $categorieprogramme = $this->getDoctrine()->getManager()
        ->getRepository(CategorieProgramme::class)
        ->find($request->get("categorie_programme_id"));
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->find($request->get("user_id"));
        
        $titre = $request->query->get("titre");
        $image = $request->query->get("image");
        $description = $request->query->get("description");
        $jaime = $request->query->get("jaime");
        $jaimepas = $request->query->get("jaimepas");
        
        
        
        $programme->setCategorieProgramme($categorieprogramme);
        $programme->setTitre($titre);
        $programme->setDescription($description);
        $programme->setImage($image);
        $programme->setJaime(0);
        $programme->setUser($user);
        $programme->setJaimepas(0);

        
        
        $em->persist($programme);
        $em->flush();

        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(1);
// Add Circular reference handler
        $normalizer->setCircularReferenceHandler(function ($object) {
        
});  
$rep = $this->getDoctrine()->getRepository(Programme::class);
        $programmes = $rep->findAll();
        $normalizers = array($normalizer);
        $serializer = new Serializer($normalizers, $programmes);
        $formatted = $serializer->normalize($programmes);
       
       // return new JsonResponse("Reclamation a ete modifiee avec success.");
        return new JsonResponse($formatted);
            

      
       
    }



/**
     * @Route("/updateProgramme/{id}", name="updateProgramme")
     */
    public function updateProgramme(Request $request ): Response
    {
        $em = $this->getDoctrine()->getManager();
        $reclamation = $this->getDoctrine()->getManager()
                        ->getRepository(Programme::class)
                        ->find($request->get("id"));

        $reclamation->setTitre($request->get("titre"));
        $reclamation->setDescription($request->get("description"));
        $reclamation->setImage($request->get("image"));


        $em->persist($reclamation);
        $em->flush();
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(1);
// Add Circular reference handler
        $normalizer->setCircularReferenceHandler(function ($object) {
        
});  
$rep = $this->getDoctrine()->getRepository(Programme::class);
        $programmes = $rep->findAll();
        $normalizers = array($normalizer);
        $serializer = new Serializer($normalizers, $programmes);
        $formatted = $serializer->normalize($programmes);
       
       // return new JsonResponse("Reclamation a ete modifiee avec success.");
        return new JsonResponse($formatted);
        
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
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize("programme supprimer");

         return new JsonResponse($formatted);
    
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
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize("programme vu");

         return new JsonResponse($formatted);
    }

/**
     * @param $id
     * @param ProgrammeRepository $repository;
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/likeeventPr/{id}", name="likeeventPr")
     */
public function likeEvent( $id )
    {
       $rep = $this->getDoctrine()->getRepository(Programme::class);
        $em = $this->getDoctrine()->getManager();
        $programme=$rep->find($id);
        $new=$programme->getJaime() + 1;
        $programme->setJaime($new);
        $this->getDoctrine()->getManager()->flush();
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(1);
// Add Circular reference handler
        $normalizer->setCircularReferenceHandler(function ($object) {
        
});  
$rep = $this->getDoctrine()->getRepository(CategorieProgramme::class);
        $programmes = $rep->findAll();
        $normalizers = array($normalizer);
        $serializer = new Serializer($normalizers, $programmes);
        $formatted = $serializer->normalize($programmes);
       
       // return new JsonResponse("Reclamation a ete modifiee avec success.");
        return new JsonResponse($formatted);
    }
    /**
     * @param $id
     * @param ProgrammeRepository $repository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/dislikeeventPr/{id}", name="dislikeeventPr")
     */
    public function dislikeEvent($id )
    {
        $rep = $this->getDoctrine()->getRepository(Programme::class);
        $em = $this->getDoctrine()->getManager();
        $programme=$rep->find($id);
        $new=$programme->getJaimepas() + 1;
        $programme->setJaimepas($new);
        $this->getDoctrine()->getManager()->flush();
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(1);
// Add Circular reference handler
        $normalizer->setCircularReferenceHandler(function ($object) {
        
});  
$rep = $this->getDoctrine()->getRepository(CategorieProgramme::class);
        $programmes = $rep->findAll();
        $normalizers = array($normalizer);
        $serializer = new Serializer($normalizers, $programmes);
        $formatted = $serializer->normalize($programmes);
       
       // return new JsonResponse("Reclamation a ete modifiee avec success.");
        return new JsonResponse($formatted);
    }
    


/**
     * @Route("/newJson", name="newJson", methods={"GET", "POST"})
     */
    public function newJson(Request $request, EntityManagerInterface $entityManager): Response
    {
        
        $categorieProgramme= new categorieProgramme();
        $description = $request->query->get("description");
        $libelle = $request->query->get("libelle");
        $image = $request->query->get("image");
        $em = $this->getDoctrine()->getManager();
        
        $categorieProgramme->setLibelle($libelle);
        $categorieProgramme->setDescription($description);
        $categorieProgramme->setImage($image);
        $categorieProgramme->setJaime(0);
        $categorieProgramme->setJaimepas(0);

        
        
        $em->persist($categorieProgramme);
        $em->flush();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($categorieProgramme);
        return new JsonResponse($formatted);
    }

// ------------------------------------------------------------------------------------------------------------


     /**
     * @Route("/listJson", name="listJson", methods={"GET"})
     */
    public function listJson(): Response
    {
        $rep = $this->getDoctrine()->getRepository(CategorieProgramme::class);
        $programmes = $rep->findAll();
       
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(1);
// Add Circular reference handler
        $normalizer->setCircularReferenceHandler(function ($object) {
        
});
        $normalizers = array($normalizer);
        $serializer = new Serializer($normalizers, $programmes);
        $formatted = $serializer->normalize($programmes);

         return new JsonResponse($formatted);
        
    }

// ------------------------------------------------------------------------------------------------------------

/**
     * @Route("/editJason/{id}", name="editJason", methods={"GET", "POST"})
     */
    public function editJason(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $reclamation = $this->getDoctrine()->getManager()
                        ->getRepository(CategorieProgramme::class)
                        ->find($request->get("id"));

        $reclamation->setLibelle($request->get("libelle"));
        $reclamation->setDescription($request->get("description"));
        $reclamation->setImage($request->get("image"));


        $em->persist($reclamation);
        $em->flush();
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(1);
// Add Circular reference handler
        $normalizer->setCircularReferenceHandler(function ($object) {
        
});  
$rep = $this->getDoctrine()->getRepository(CategorieProgramme::class);
        $programmes = $rep->findAll();
        $normalizers = array($normalizer);
        $serializer = new Serializer($normalizers, $programmes);
        $formatted = $serializer->normalize($programmes);
       
       // return new JsonResponse("Reclamation a ete modifiee avec success.");
        return new JsonResponse($formatted);
            

      //-----------------------------------------------------------------------------------------------------
    }
 /**
     * @Route("deleteJson/{id}", name="deleteJson", methods={"GET", "POST"})
     */
    public function deleteJson(Request $request, CategorieProgramme $categorieProgramme, EntityManagerInterface $entityManager): Response
    {
         
            $entityManager->remove($categorieProgramme);
            $entityManager->flush();
            $serializer = new Serializer([new ObjectNormalizer()]);
            $formatted = $serializer->normalize("categorie supprimer");
 
             return new JsonResponse($formatted);
        
    }











}
