<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\Connexion;
use App\Entity\Utilisateur;
use App\Form\Inscription;

class ConnexionController extends AbstractController
{
    #[Route('/connexion', name: 'app_connexion')]
    public function index(Request $request): Response
    {
        $utilisateurConnexion = new Utilisateur();
        $connexionForm = $this->createForm(Connexion::class, $utilisateurConnexion); 
        $connexionForm->handleRequest($request); 
        if ($connexionForm->isSubmitted() && $connexionForm->isValid()) { 
            $data = $connexionForm->getData(); 
            return $this->redirectToRoute('app_connexion', [], response::HTTP_SEE_OTHER); 
        } 

        $utilisateurInscription = new Utilisateur();
        $inscritpionForm = $this->createForm(Inscription::class, $utilisateurInscription); 
        $inscritpionForm->handleRequest($request); 
        if ($inscritpionForm->isSubmitted() && $inscritpionForm->isValid()) { 
            $data = $inscritpionForm->getData(); 
            return $this->redirectToRoute('app_connexion', [], response::HTTP_SEE_OTHER); 
        } 

        return $this->render('connexion/connexion.html.twig', [
            'connexionForm' => $connexionForm->createView(),
            'inscriptionForm'=> $inscritpionForm->createView(),
            'controller_name' => 'ConnexionController',
        ]);
    }
}
