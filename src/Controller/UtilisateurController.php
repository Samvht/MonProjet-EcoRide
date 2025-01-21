<?php

namespace App\Controller;

use App\Form\Proposer;
use App\Form\RoleMetier;
use App\Form\Vehicule;
use App\Repository\VoitureRepository;
use App\Entity\Covoiturage;
use App\Entity\Utilisateur;
use App\Entity\Role;
use App\Entity\Voiture;
use App\Entity\Marque;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UtilisateurController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/utilisateur', name: 'app_utilisateur')]
    public function index(): Response
    {
        return $this->render('utilisateur/utilisateur.html.twig', [
            'controller_name' => 'UtilisateurController',
        ]);
    }

    #[Route('/utilisateur/moncompte', name: 'moncompte')]
    public function monCompte(Request $request, EntityManagerInterface $entityManager, VoitureRepository $voitureRepository): Response
    {
        #récupère l'utilisateur connecté
        $utilisateur = $this->getUser();

        #création formulaire
        $roleMetier = new Role();
        $roleForm = $this->createForm(roleMetier::class, $roleMetier);
        $roleForm->handleRequest($request);

        if($roleForm->isSubmitted() && $roleForm->isValid()){
            $entityManager->flush(); #POur enregistrer le role
            $this->addFlash('success', 'Rôle mis à jour avec succès!');
        }

        #Récupérer la liste des véhicules de l'utilisateur
        #utilisation querybuider, car findby erreur, ne retrouve pas 'libelle'
        $voiture = $entityManager->getRepository(Voiture::class)
        ->createQueryBuilder('v')
        ->leftJoin('v.marque', 'm')
        ->addSelect('m')
        ->where('v.utilisateur = :utilisateur')
        ->setParameter('utilisateur', $utilisateur)
        ->getQuery()
        ->getResult();
    

        return $this->render('utilisateur/moncompte.html.twig', [
            'roleForm' => $roleForm->createView(),
            'voiture' => $voiture,
        ]);
    }

    

    #[Route('/utilisateur/moncompte/vehicule', name: 'vehicule')]
    public function ajouterVehicule(Request $request, EntityManagerInterface $entityManager): Response
    {

        $vehicule = new Voiture();
        $vehiculeForm = $this->createForm(Vehicule::class, $vehicule);
        $vehiculeForm->handleRequest($request);

        if ($vehiculeForm->isSubmitted() && $vehiculeForm->isValid()){
            $entityManager->persist($vehicule);
            $entityManager->flush();

            return $this->redirectToRoute('moncompte', [], response::HTTP_SEE_OTHER);
        }
        return $this->render('utilisateur/vehicule.html.twig', [
            'vehiculeForm' => $vehiculeForm->createView(),
        ]);
    }

    #[Route('/utilisateur/proposercovoiturage', name: 'proposercovoiturage', methods:['GET', 'POST'])]
    public function proposerCovoiturage(Request $request): Response
    {
        #création formulaire
        $covoiturage = new Covoiturage();
        $proposerForm = $this->createForm(Proposer::class, $covoiturage); 
        $proposerForm->handleRequest($request); 
        #soummission formulaire et récupération données
        if ($proposerForm->isSubmitted() && $proposerForm->isValid()) { 
        
            $covoiturageData = $proposerForm->getData();
            #envoi nouveau covoit dans la BDD
            $this->entityManager->persist($covoiturageData);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_utilisateur', [], response::HTTP_SEE_OTHER); 
        } 
        
        return $this->render('utilisateur/proposercovoiturage.html.twig', [
            'proposerForm' => $proposerForm->createView(),
        ]);
    }
}
