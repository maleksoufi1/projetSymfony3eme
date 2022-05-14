<?php

namespace App\Controller;

use App\Repository\CategorieRegimeRepository;
use App\Repository\RegimeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class StatistiqueController extends AbstractController
{
    /**
     * @Route("/statistique", name="statistique")
     */
    public function index(): Response
    {
        return $this->render('statistique/index.html.twig', [
            'controller_name' => 'StatistiqueController',
        ]);
    }

    /**
     * @Route("/statRegime", name="statRegime")
     */
    public function statRegime(CategorieRegimeRepository $categorieRegimeRepository , RegimeRepository $regimeRepository): Response
    {
      
        //toutes les cat 
        $categories = $categorieRegimeRepository->findAll();
        // les tableaux pour js chart canvas
        $NomCateg= [];
        $ColorCateg= [];
        $CountCateg= [];
        foreach($categories as $categorie){
            $NomCateg[] = $categorie->getLibelle();
            $ColorCateg[] = $categorie->getStatcolor();
            // nombre de régime par catégorie 
            $CountCateg[] = count($categorie->getRegimes());
        }

        //toutes les reg 
        $regimes = $regimeRepository->findAll();
        // les tableaux pour js chart canvas
        $NomReg= [];
        $CountSuivi= [];
        foreach($regimes as $regime){
            $NomReg[] = $regime->getType();
            // nombre de régime par catégorie 
            $CountSuivi[] = count($regime->getSuivisRegimes());
        }

       // nbr de regime publier par date 
       $regsDate = $regimeRepository->countByDate();

        $dates = [];
        $regDateCount=[];
        foreach($regsDate as $regDate){
            $dates[] = $regDate['dateRegimes'];
            $regDateCount[] = $regDate['count'];
        }

        //en envoyer les données en json car en chart js les format de données sont des tableaux
        return $this->render('statistique/statCatRegime.html.twig', [
            'NomCateg' => json_encode($NomCateg),
            'ColorCateg' => json_encode($ColorCateg),
            'CountCateg' => json_encode($CountCateg),

            'NomReg' => json_encode($NomReg),
            'CountSuivi' => json_encode($CountSuivi),

            'dates' => json_encode($dates),
            'regDateCount' => json_encode($regDateCount),


        ]);
    }


}
