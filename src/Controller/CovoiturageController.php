<?php

namespace App\Controller;


use App\Form\Rechercher;
use App\Entity\Covoiturage;
use App\Entity\Utilisateur;
use App\Document\Preference;
use Doctrine\ODM\MongoDB\DocumentManager;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CovoiturageRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\RoleService;



class CovoiturageController extends AbstractController
{
    private $entityManager;
    private LoggerInterface $logger; 
    private $roleService; 
    private $preferences;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger, RoleService $roleService, Preference $preferences)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this ->roleService = $roleService;
        $this ->preferences = $preferences;
    }

    #[Route('/covoiturage', name: 'app_covoiturage', methods:['GET', 'POST'])]
    public function index(Request $request, CovoiturageRepository $covoiturageRepository, LoggerInterface $logger): Response
    {
        #$logger->info('Contrôleur atteint');

        $covoiturage = new Covoiturage();
        $form = $this->createForm(Rechercher::class, $covoiturage); 
        $form->handleRequest($request); 
        

        $results = [];
        $page = $request->query->getInt('page', 1);
        $itemsPerPage = 10;

        if ($form->isSubmitted() && $form->isValid()) { 
            
            $lieuDepart = $form->get('lieu_depart')->getData();
            $lieuArrivee = $form->get('lieu_arrivee')->getData();
            $dateDepart = $form->get('date_depart')->getData();
            
           
            # Recherche des covoiturages correspondant aux critères
            $resultsQuery = $covoiturageRepository->searchCovoiturages($lieuDepart, $lieuArrivee, $dateDepart, $itemsPerPage, ($page - 1) * $itemsPerPage);
            $results = $resultsQuery['results'];
            $totalResults = $resultsQuery['total'];

            # Si aucun covoiturage trouvé, suggérer la date la plus proche
            if ( $totalResults === 0) {
                $closestCovoiturageData = $covoiturageRepository->findClosestCovoiturage($lieuDepart, $lieuArrivee, $dateDepart);
                if ($closestCovoiturageData['result'] !== null) {
                    $closestCovoiturage = $closestCovoiturageData['result'];
                    $suggestedDate = \DateTime::createFromFormat('d/m/Y', $closestCovoiturage->getDateDepart());
                } else {
                    $closestCovoiturage = null;
                    $suggestedDate = null;
                }
            } else {
                $suggestedDate = null;
            }
            $totalPages = ceil($totalResults / $itemsPerPage);
        } else {
            $suggestedDate = null;
            $totalPages = 1;

        }
        
        return $this->render('covoiturage/covoiturage.html.twig', [
            'form' => $form->createView(),
            'results' => $results,
            'suggestedDate' => $suggestedDate,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'controller_name' => 'CovoiturageController',
        ]);
    }

    
    
    #[Route('/covoiturage/detail/{covoiturage_id}', name: 'detail', methods:['GET', 'POST'])]
    public function detail(int $covoiturage_id, EntityManagerInterface $entityManager, DocumentManager $dm): Response
    { 
        $covoiturage = $entityManager->getRepository(Covoiturage::class)->find($covoiturage_id);

        if (!$covoiturage) {
            throw $this->createNotFoundException('Covoiturage non trouvé');
        }

        #récupération info voiture et marque en fonction covoiturage_id
        $voiture = $covoiturage->getVoiture();
        $marque = $voiture->getMarque();

        #récupère role métier (chauffeur, passager ou les 2)
        $rolesMetier = $this->roleService->getUserRolesMetier();

        #récupération préférences utilisateur
        $utilisateur = $covoiturage->getCreateur();
        $preferences = $dm->getRepository(Preference::class)->findOneBy(['utilisateur_id' => $utilisateur->getUtilisateurId()]);


        return $this->render('covoiturage/detail.html.twig', [
            'covoiturage' => $covoiturage,
            'voiture' => $voiture,
            'marque' => $marque,
            'rolesMetier'=> $rolesMetier,
            'utilisateur' => $utilisateur,
            'preferences' => $preferences,
        ]);
    }

    #route pour action de participer
    #[Route('/participer/{covoiturage_id}', name: 'covoiturage_participer', methods: ['POST'])]
    public function participer(int $covoiturage_id, EntityManagerInterface $entityManager): Response
    {
        #récupère utilisateur connecté ou bien le redirige pour connexion
        $utilisateur = $this->getUser();
        if (!$utilisateur) {
            return $this->redirectToRoute('app_connexion');
        }

        $covoiturage = $entityManager->getRepository(Covoiturage::class)->find($covoiturage_id);
        if (!$covoiturage) {
            throw $this->createNotFoundException('Covoiturage non trouvé');
        }

        #mets à jour le nbre de place dispo + envoi le tout dans la BDD
        if ($covoiturage->getNbrePlace() > 0 && !$covoiturage->getUtilisateurs()->contains($utilisateur)) {
            $covoiturage->addUtilisateur($utilisateur);
            $covoiturage->setNbrePlace($covoiturage->getNbrePlace() - 1);
            $entityManager->persist($covoiturage);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_utilisateur');
    }
    
}
