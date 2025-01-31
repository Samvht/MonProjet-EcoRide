<?php

namespace App\Controller;


use App\Form\Rechercher;
use App\Entity\Covoiturage;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CovoiturageRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



class CovoiturageController extends AbstractController
{
    private $entityManager;
    private LoggerInterface $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    #[Route('/covoiturage', name: 'app_covoiturage', methods:['GET', 'POST'])]
    public function index(Request $request, CovoiturageRepository $covoiturageRepository, LoggerInterface $logger): Response
    {
        #$logger->info('Contrôleur atteint');

        $covoiturage = new Covoiturage();
        $form = $this->createForm(Rechercher::class, $covoiturage); 
        $form->handleRequest($request); 

        $results = [];

        if ($form->isSubmitted() && $form->isValid()) { 
            
            $lieuDepart = $form->get('lieu_depart')->getData();
            $lieuArrivee = $form->get('lieu_arrivee')->getData();
            $dateDepart = $form->get('date_depart')->getData();
            
           
            # Recherche des covoiturages correspondant aux critères
            $results = $covoiturageRepository->searchCovoiturages($lieuDepart, $lieuArrivee, $dateDepart);
            
    }
    
        
        return $this->render('covoiturage/covoiturage.html.twig', [
            'form' => $form->createView(),
            'results' => $results,
            'controller_name' => 'CovoiturageController',
        ]);
    }
    
    #[Route('/covoiturage/detail/{covoiturage_id}', name: 'detail', methods:['GET', 'POST'])]
    public function detail(int $covoiturage_id, EntityManagerInterface $entityManager): Response
    { 
        $covoiturage = $entityManager->getRepository(Covoiturage::class)->find($covoiturage_id);

        if (!$covoiturage) {
            throw $this->createNotFoundException('Covoiturage non trouvé');
        }

        return $this->render('covoiturage/detail.html.twig', [
            'covoiturage' => $covoiturage,
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
