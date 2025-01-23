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
use App\Form\Modification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

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
        $roleMetierForm = $this->createForm(roleMetier::class, $roleMetier);
        $roleMetierForm->handleRequest($request);

        if($roleMetierForm->isSubmitted() && $roleMetierForm->isValid()){
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
            'utilisateur' => $utilisateur,
            'roleMetierForm' => $roleMetierForm->createView(),
            'voiture' => $voiture,
        ]);
    }

    #[Route('/utilisateur/moncompte/modification', name: 'modification')]
    public function ModifierProfil(Request $request, EntityManagerInterface $entityManager): Response
    {
        $utilisateur = $this->getUser();
        
        $modificationForm = $this->createForm(Modification::class, $utilisateur); 
        
        $modificationForm->handleRequest($request); 
        if ($modificationForm->isSubmitted() && $modificationForm->isValid()) { 
            #Si utilisateur modifie mot de passe
            if (!empty($utilisateur>getPassword())) {
                #pour hacher le nouveau mot de passe
                $utilisateur->setPassword(password_hash($utilisateur->getPassword(), PASSWORD_DEFAULT));
            }

            #Gestion du fichier photo
            $photo = $modificationForm->get('photo')->getData();
            if ($photo) {
                # Générer un nom unique pour la photo
                $newFilename = uniqid() . '.' . $photo->guessExtension();

                # Déplacer le fichier dans le répertoire de stockage
                try {
                    $photo->move(
                        $this->getParameter('photos_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    #Si une erreur se produit lors du déplacement du fichier, on peut afficher une erreur
                    $this->addFlash('error', 'Erreur lors de l\'upload de la photo.');
                    return $this->redirectToRoute('modification_compte');
                }

                $utilisateur->setPhoto($newFilename);
            }
            
            $entityManager->persist($utilisateur);
            $entityManager->flush();

            return $this->redirectToRoute('moncompte', [], response::HTTP_SEE_OTHER);
        }

        return $this->render('utilisateur/modification.html.twig', [
            'modificationForm' => $modificationForm->createView(),
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
