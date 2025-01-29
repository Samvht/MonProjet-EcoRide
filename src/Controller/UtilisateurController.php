<?php

namespace App\Controller;

use App\Form\Proposer;
use App\Form\RoleMetier;
use App\Form\Vehicule;
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
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UtilisateurController extends AbstractController
{
    private $entityManager;
    private LoggerInterface $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    #[Route('/utilisateur', name: 'app_utilisateur')]
    public function utilisateur(): Response
    {
        return $this->render('utilisateur/utilisateur.html.twig', [
            'controller_name' => 'UtilisateurController',
        ]);
    }

    #[Route('/utilisateur/moncompte', name: 'moncompte')]
    public function monCompte(Request $request, EntityManagerInterface $entityManager): Response
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
        #utilisation querybuider, car findby erreur, ne retrouve pas 'libelle' car dans entité marque et non voiture
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
    public function ModifierProfil(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        #redirection si utilisateur connecté
        $utilisateur = $this->getUser();

        # Journalisation de $_FILES avant traitement du formulaire
        $this->logger->info('$_FILES avant traitement du formulaire', ['files' => $_FILES]);
        
        #Sauvegarde mot de passe actuel
        $currentPassword = $utilisateur->getPassword();

        $modificationForm = $this->createForm(Modification::class, $utilisateur); 
        $modificationForm->handleRequest($request);

        if ($modificationForm->isSubmitted() && $modificationForm->isValid()) { 
            
            #Si utilisateur modifie mot de passe
            $newPassword = $modificationForm->get('password')->getData();
            if (!empty($newPassword)) {
                #pour hacher le nouveau mot de passe
                $utilisateur->setPassword($passwordHasher->hashPassword($utilisateur, $newPassword));
            } else{
                #conserver l'ancien mot de passe
                $utilisateur->setPassword($currentPassword);
            }
        
            
            #Gestion du fichier photo
            $photo = $modificationForm->get('photo')->getData();
            if (($photo)) {
                $this->logger->info('Photo reçue', ['photo' => $photo->getClientOriginalName()]);

        
                
                # Générer un nom unique pour la photo
                $newFilename = uniqid() . '.' . $photo->guessExtension();

                # Déplacer le fichier dans le répertoire de stockage
                try {
                    $photo->move(
                        $this->getParameter('photos_directory'),
                        $newFilename
                    );
                    $this->logger->info('Photo déplacée avec succès');
                    
                } catch (FileException $e) {
                    
                    $this->logger->error('Erreur lors de l\'upload de la photo', ['exception' => $e->getMessage()]);
                    #Si une erreur se produit lors du déplacement du fichier, on peut afficher une erreur
                    $this->addFlash('error', 'Erreur lors de l\'upload de la photo.');
                    return $this->redirectToRoute('modification');
                }

                $utilisateur->setPhoto($newFilename);
            }

            #récupération date naissance (telephone fait automatiquement)
            $utilisateur->setDateNaissance($modificationForm->get('date_naissance')->getData());
            
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
