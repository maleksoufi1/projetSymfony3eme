<?php

namespace App\Controller;

use App\Data\FiltreData;
use App\Entity\User;
use App\Entity\Regime;
use App\Form\AddRegimeType;
use Doctrine\ORM\Mapping\Id;
use App\Entity\CategorieRegime;
use App\Form\FiltreForm;
use App\Repository\RegimeRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class RegimeController extends AbstractController
{
    /**
     * @Route("/regime", name="regime")
     */
    public function index(Request $request,PaginatorInterface $paginator ): Response
    {
        $rep = $this->getDoctrine()->getRepository(Regime::class);
        $allregimes = $rep->findAll();
        $rep = $this->getDoctrine()->getRepository(CategorieRegime::class);
        $Catregimes = $rep->findAll();
        
        $regimes = $paginator->paginate(
            // Doctrine Query, not results
            $allregimes,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            3
        );
       
        
        return $this->render('regime/index.html.twig', [
            'regimes'=>$regimes,
            'Catregimes'=>$Catregimes
        ]);
      
    }


   




    

     /**
     * @Route("/regimes", name="regimes")
     *  @IsGranted("ROLE_CLIENT","ROLE_NUTRIONNISTE")
     */
    public function regimes(Request $request,PaginatorInterface $paginator , RegimeRepository $regimeRepository ): Response
    {


        $data = new FiltreData();
        $data->page = $request->get('page', 1);
        $form = $this->createForm(FiltreForm::class, $data);
        $form->handleRequest($request);
        [$min,$max]= $regimeRepository->MinMax($data);
        
        $allregimes = $regimeRepository->findSearch($data);
       
      
        
        $regimes = $paginator->paginate(
            // Doctrine Query, not results
            $allregimes,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            3
        );
       
        
        return $this->render('regime/allRegime.html.twig', [
            'regimes'=>$regimes,
            'form' => $form->createView(),
            'min'=> $min,
            'max'=> $max
        ]);
      
    }

   









     /**
     * @Route("/listRegimes", name="listRegimes" )
     * @IsGranted("ROLE_ADMIN","ROLE_NUTRIONNISTE")
     */
    public function list(RegimeRepository $regimeRepository , SerializerInterface $serializerInterface,PaginatorInterface $paginator ,Request $request): Response
    {
        //va etre variable session

        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->find($this->getUser());
        

        $allregimes = $regimeRepository->findListRegimeByIdUser($user);
        $json = $serializerInterface->serialize($allregimes , 'json' , ['groups'=>'regime']);
        $regimes = $paginator->paginate(
            // Doctrine Query, not results
            $allregimes,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            3
        );
        return $this->render('regime/listRegimes.html.twig', [
          'regimes'=>$regimes,
     ]);
     //return new JsonResponse($json);

        
    }

    /**
     * @Route("/detailRegime/{id}", name="detailRegime")
     * @IsGranted("ROLE_CLIENT","ROLE_NUTRIONNISTE")
     */
    public function detailRegime($id): Response
    {
        $rep = $this->getDoctrine()->getRepository(Regime::class);
        $regime = $rep->find($id);
 
        return $this->render('regime/detailRegime.html.twig', [
          'regime'=>$regime,
     ]);
        
    }
   
    


     /**
     * @Route("/addRegime", name="addRegime")
     * @IsGranted("ROLE_ADMIN","ROLE_NUTRIONNISTE")
     */
    public function addRegime(Request $request, \Swift_Mailer $mailer): Response
    {
 //va etre variable session
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->find($this->getUser());
      

        $regime = new Regime();
        $form = $this->createForm(AddRegimeType::class , $regime);
        $form = $form->handleRequest($request);

        if ($form->isSubmitted()&& $form->isValid()){
            $regime = $form->getData();
            $regime->setUser($user);
            $file = $regime->getImage();
            $fileName = md5(uniqid()).'.'.$file->guessExtension() ;
            try{
                $file->move(
                $this->getParameter('images_directory'),
                $fileName
            );
            }catch(FileException $e){

            }
            
            $em = $this->getDoctrine()->getManager();
            $regime->setImage($fileName);
            $em->persist($regime);
            $em->flush();
            //envoie email success d'ajout regime
            $userEmail = $user->getEmail();
            $message = (new \Swift_Message('New'))

            ->setFrom('houssem.kouki@esprit.tn')

            ->setTo($userEmail )

            ->setSubject('Votre régime a été enregistrée !')
            ->setBody( $this->renderView(
                'regime/addRegimeEmail.html.twig'),
               
                'text/html'
            );
            $mailer->send($message); 
            return $this->redirectToRoute('listRegimes');
        }


        return $this->render('regime/addRegime.html.twig', [
            'formAddRegime'=>$form->createView(),
        ]);
    }





    //template regime email
     /**
     * @Route("/TemplateRegime", name="TemplateRegime")
     * @IsGranted("ROLE_CLIENT","ROLE_NUTRIONNISTE")
     */
    public function TemplateRegime(Request $request,PaginatorInterface $paginator): Response
    {
        
        return $this->render('regime/addRegimeEmail.html.twig', [
        ]);
    }


























     /**
     * @Route("/updateRegime/{id}", name="updateRegime")
     */
    public function updateRegime(Request $request , $id): Response
    {
       
        $rep = $this->getDoctrine()->getRepository(Regime::class);
        $regime  = $rep->find($id);
        $form = $this->createForm(AddRegimeType::class , $regime);
        $form = $form->handleRequest($request);
        if ($form->isSubmitted()&& $form->isValid()){
            $regime = $form->getData();
            $file = $regime->getImage();
            $fileName = md5(uniqid()).'.'.$file->guessExtension() ;
            try{
                $file->move(
                $this->getParameter('images_directory'),
                $fileName
            );
            }catch(FileException $e){

            }
             $em = $this->getDoctrine()->getManager();
             $regime->setImage($fileName);
             $em->flush();
             return $this->redirectToRoute('listRegimes');
         }
 
        return $this->render('regime/updateRegime.html.twig', [
            'formUpdateRegime'=> $form->createView(),
            'regime'=>$regime
     ]);
        
    }
  /**
     * @Route("/deleteRegime/{id}", name="deleteRegime")
     */
    public function deleteRegime($id): Response
    {
        $rep = $this->getDoctrine()->getRepository(Regime::class);
        $em = $this->getDoctrine()->getManager();
        $regime = $rep->find($id);
        $em->remove($regime);
        $em->flush();
        return $this->redirectToRoute('listRegimes');
    }


    


     /**
     * @Route("/searchRegime", name="searchRegime")
     */
    public function searchRegime(Request $request , RegimeRepository $regimeRepository){
        $em = $this->getDoctrine()->getManager();
        $requestString = $request->get('q');
        $posts =  $regimeRepository->findEntitiesByString($requestString);
        if(!$posts) {
            $result['posts']['error'] = "Pas de régime ! :( ";
        } else {
            $result['posts'] = $this->getRealEntities($posts);
        }
        return new Response(json_encode($result));
    }
    public function getRealEntities($posts){
        foreach ($posts as $posts){
            $realEntities[$posts->getId()] = [$posts->getImage(),$posts->getType()];

        }
        return $realEntities;
    }






    // Les fonctions Api

    /**
     * @Route("/AllRgimes", name="AllRgimes")
     */
    public function AllRgimes(NormalizerInterface $normalizer){
      
        $rep = $this->getDoctrine()->getRepository(Regime::class);
        $regimes = $rep->findAll();

        $json = $normalizer->normalize($regimes , 'json' , ['groups'=>'regime']);

        return new Response(json_encode($json));
    }

    


}
