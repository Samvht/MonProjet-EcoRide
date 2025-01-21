<?php

namespace App\Controller;


use App\Form\Rechercher;
use App\Entity\Covoiturage;
use App\Repository\CovoiturageRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class CovoiturageController extends AbstractController
{
    #[Route('/covoiturage', name: 'app_covoiturage', methods:['GET', 'POST'])]
    public function index(Request $request): Response
    {

        $covoiturage = new Covoiturage();
        $form = $this->createForm(Rechercher::class, $covoiturage); 
        $form->handleRequest($request); 
        if ($form->isSubmitted() && $form->isValid()) { 
            $data = $form->getData(); 
            return $this->redirectToRoute('app_covoiturage', [], response::HTTP_SEE_OTHER); 
        } 
        
        return $this->render('covoiturage/covoiturage.html.twig', [
            'form' => $form->createView(),
            'controller_name' => 'CovoiturageController',
        ]);
    }

    public function rechercher(Request $request, CovoiturageRepository $covoiturageRepository): Response
    {
        #Récupérer les paramètres de recherche depuis l'URL
        $LieuDepart = $request->query->get('lieu_depart');
        $LieuArrivee = $request->query->get('lieu_arrivee');
        $DateDepart = $request->query->get('date_depart');
    
        #Recherche des covoiturages correspondant aux critères
        $results = $covoiturageRepository->searchCovoiturages($LieuDepart, $LieuArrivee, $DateDepart);
    
        #Retourner la vue avec les résultats
        return $this->render('covoiturage/covoiturage.html.twig', [
                'results' => $results,
        ]);
    }
    

    #A decommenter lorsque trouver moyen au clic participer si connecté renvoi vers /utilisateur, si non connecté, renvoi vers /connexion
    ##[Route('/covoiturage/participer/{id}', name: 'covoiturage_participer')] 
    #public function participer(int $id): Response 
    #{ 
        #vérification utilisateur connecté ou non
    #    if (!$this->isGranted('ROLE_USER')) { 
    #        return $this->redirectToRoute('app_connexion'); 
    #    } 
            #si utilisateur connecté peut participer au covoit
    #        return $this->redirectToRoute('app_utilisateur'); [ 
    #            'id' => $id 
    #        ]); 
    #    }
}
