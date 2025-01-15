<?php

namespace App\Controller;

use App\Entity\Covoiturage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\Proposer;

class UtilisateurController extends AbstractController
{
    #[Route('/utilisateur', name: 'app_utilisateur')]
    public function index(): Response
    {
        return $this->render('utilisateur/utilisateur.html.twig', [
            'controller_name' => 'UtilisateurController',
        ]);
    }

    #[Route('/utilisateur/proposercovoiturage', name: 'proposercovoiturage', methods:['GET', 'POST'])]
    public function proposerCovoiturage(Request $request): Response
    {

        $covoiturage = new Covoiturage();
        $proposerForm = $this->createForm(Proposer::class, $covoiturage); 
        $proposerForm->handleRequest($request); 
        if ($proposerForm->isSubmitted() && $proposerForm->isValid()) { 
            $data = $proposerForm->getData(); 
            return $this->redirectToRoute('app_covoiturage', [], response::HTTP_SEE_OTHER); 
        } 
        
        return $this->render('utilisateur/proposercovoiturage.html.twig', [
            'proposerForm' => $proposerForm->createView(),
        ]);
    }
}
