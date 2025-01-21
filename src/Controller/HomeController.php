<?php

namespace App\Controller;

use App\Form\Rechercher;
use App\Entity\Covoiturage;
use App\Repository\CovoiturageRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/accueil', name: 'app_home', methods:['GET', 'POST'])]
    public function index(Request $request, CovoiturageRepository $covoiturageRepository): Response
    { 
        #création formulaire
        $covoiturage = new Covoiturage();
        $form = $this->createForm(Rechercher::class, $covoiturage); 
        $form->handleRequest($request); 
        if ($form->isSubmitted() && $form->isValid()) { 
            #Récupération des données 
            $data = $form->getData(); 

            #rediriger vers la page covoiturage avec les critères de recherche dans l'url
            return $this->redirectToRoute('app_covoiturage', [
                    'Lieu_depart' => $data->getLieuDepart(),
                    'Lieu_arrivee' => $data->getLieuDepart(),
                    'Date_depart' => $data->getLieuDepart(),
            ], response::HTTP_SEE_OTHER); 
        } 
        
        //ce qui sera affiché dans la page d'accueil
        return $this->render('home/index.html.twig', [
            'form' => $form->createView(),
            'controller_name' => 'HomeController',
        ]);
    }
}
