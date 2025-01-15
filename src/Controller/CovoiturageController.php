<?php

namespace App\Controller;


use App\Entity\Covoiturage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\Rechercher;


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

    #A decommenter lorsque la page /covoiturage/participer sera crée, redirection vers cette page
    ##[Route('/covoiturage/participer/{id}', name: 'covoiturage_participer')] 
    #public function participer(int $id): Response 
    #{ 
        #vérification utilisateur connecté ou non
    #    if (!$this->isGranted('ROLE_USER')) { 
    #        return $this->redirectToRoute('login'); 
    #    } 
            #si utilisateur connecté peut participer au covoit
    #        return $this->render('covoiturage/participer.html.twig', [ 
    #            'id' => $id 
    #        ]); 
    #    }
}
