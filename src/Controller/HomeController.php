<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\Rechercher;
use App\Entity\Covoiturage;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods:['GET', 'POST'])]
    public function index(Request $request): Response
    { 
        $covoiturage = new Covoiturage();
        $form = $this->createForm(Rechercher::class, $covoiturage); 
        $form->handleRequest($request); 
        if ($form->isSubmitted() && $form->isValid()) { 
            $data = $form->getData(); 
            return $this->redirectToRoute('app_covoiturage', [], response::HTTP_SEE_OTHER); 
        } 
        
        //ce qui sera affiché dans la page d'accueil
        return $this->render('home/index.html.twig', [
            'form' => $form->createView(),
            'controller_name' => 'HomeController',
        ]);
    }
}
