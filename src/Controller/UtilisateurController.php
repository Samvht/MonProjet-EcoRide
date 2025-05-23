<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\Marque;
use App\Form\Proposer;
use App\Form\Vehicule;
use App\Entity\Voiture;
use App\Form\RoleMetier;
use App\Form\Preferences;
use App\Form\Modification;
use App\Entity\Covoiturage;
use App\Entity\Utilisateur;
use App\Document\Preference;
use App\Service\RoleService;
use Psr\Log\LoggerInterface;
use App\Service\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CovoiturageRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UtilisateurController extends AbstractController
{
    private $entityManager;
    private LoggerInterface $logger;
    private $emailService;
    private $roleService;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger, EmailService $emailService, RoleService $roleService)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->emailService = $emailService;
        $this->roleService = $roleService;
    }

    #[Route('/utilisateur', name: 'app_utilisateur')]
    public function utilisateur(EntityManagerInterface $entityManager, DocumentManager $dm): Response
    {
        $utilisateur = $this->getUser();
        #convertir uuid en binary pour être sûr de la récupération de l'utilisateur_id
        $uuidUtilisateur = $utilisateur->getUtilisateurId()->toBinary();
        
        #récupère role metier de l'utilisateur
        $rolesMetier = $this->roleService->getUserRolesMetier();

        #récupère les préférences de l'utilisateur
        $preferences = $dm->getRepository(Preference::class)->findOneBy(['utilisateur_id' => $utilisateur->getUtilisateurId()]);

        #Récupére les covoiturages créés par l'utilisateur
        $covoituragesCrees = [];
        foreach ($entityManager->getRepository(Covoiturage::class)->findAll() as $covoiturage) {
            if ($covoiturage->getCreateur() === $utilisateur) {
                $covoituragesCrees[] = $covoiturage;
            }
        }

        #Récupére les covoiturages auxquels l'utilisateur participe
        $covoituragesParticipes = $entityManager->createQueryBuilder()
            ->select('c')
            ->from(Covoiturage::class, 'c')
            ->innerJoin('c.utilisateurs', 'u')
            ->where('u.utilisateur_id = :utilisateur')
            ->setParameter('utilisateur', $uuidUtilisateur)
            ->getQuery()
            ->getResult();

        #Combine les deux listes de covoiturages sans doublons
        $covoiturages = array_unique(array_merge($covoituragesCrees, $covoituragesParticipes), SORT_REGULAR);


        $covoituragesWithParticipation = [];
        foreach ($covoiturages as $covoiturage) {
            $isUserParticipating = $covoiturage->getUtilisateurs()->contains($utilisateur)|| $covoiturage->getCreateur() === $utilisateur;
            $covoituragesWithParticipation[] = [
                'covoiturage' => $covoiturage,
                'isUserParticipating' => $isUserParticipating,
            ];
        }

        return $this->render('utilisateur/utilisateur.html.twig', [
            'utilisateur' =>$utilisateur,
            'covoiturages' => $covoituragesWithParticipation,
            'rolesMetier' => $rolesMetier,
            'preferences' => $preferences,
        ]);
    }
    

    #route d'action pour l'annulation du covoiturage
    #[Route('/annuler/{covoiturage_id}', name: 'covoiturage_annuler', methods: ['POST'])]
    public function annuler(Request $request, int $covoiturage_id, EntityManagerInterface $entityManager, LoggerInterface $logger): Response
    {
        #verifie utilisateur connecté ou renvoi vers la connexion
        $utilisateur = $this->getUser();
        if (!$utilisateur) {
            return $this->redirectToRoute('app_connexion');
        }

        $covoiturage = $entityManager->getRepository(Covoiturage::class)->find($covoiturage_id);
        if (!$covoiturage) {
            $logger->error('Covoiturage non trouvé', ['covoiturage_id' => $covoiturage_id]);
            throw $this->createNotFoundException('Covoiturage non trouvé');
        }

        $logger->info('Utilisateur trouvé', ['utilisateur' => $utilisateur->getUtilisateurId()]);

        #Vérification du CSRF token
        if (!$this->isCsrfTokenValid('annuler'.$covoiturage_id, $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_utilisateur');
        }

        #si l'utilisateur est le créateur
        if ($utilisateur === $covoiturage->getCreateur()) {
            #récupère les participants du covoiturage
            $participants = $covoiturage->getParticipants()->toArray();
            #Envoi tout dans la BDD
            $entityManager->remove($covoiturage);
            $entityManager->flush();

        # Envoyer un email aux participants
        $this->emailService->sendCancellationEmail($participants, $covoiturage);

        $this->addFlash('success', 'Le covoiturage a été supprimé et les participants ont été informés.');
        } else {
            #si c'est juste un utilisateur, supprime de la liste et met le nbre de place à jour dans la BDD
            $covoiturage->removeUtilisateur($utilisateur);
            $covoiturage->setNbrePlace($covoiturage->getNbrePlace() + 1);
            $entityManager->persist($covoiturage);
            $entityManager->flush();

            $logger->info('Utilisateur retiré du covoiturage', ['covoiturage' => $covoiturage->getCovoiturageId()]);
            $this->addFlash('success', 'Votre participation est bien annulée.');
            $logger->info('Covoiturage supprimé', ['covoiturage_id' => $covoiturage->getCovoiturageId()]);
    }

    return $this->redirectToRoute('app_utilisateur');
}
#Route pour action de demarrer
#[Route('/covoiturage/{covoiturage_id}/start', name: 'covoiturage_start', methods: ['POST'])]
public function startCovoiturage(int $covoiturage_id, EntityManagerInterface $em, CovoiturageRepository $repo): Response
{
    $covoiturage = $repo->find($covoiturage_id);

    if (!$covoiturage) {
        throw $this->createNotFoundException('Covoiturage non trouvé');
    }

    // Vérifier que l'utilisateur est bien le créateur
    if ($covoiturage->getCreateur() !== $this->getUser()) {
        throw $this->createAccessDeniedException("Vous n'êtes pas autorisé à démarrer ce covoiturage.");
    }

    $covoiturage->setStatut('en cours');
    $em->flush();

    return $this->redirectToRoute('app_utilisateur');
}

#Route pour action d'arreter
#[Route('/covoiturage/{covoiturage_id}/end', name: 'covoiturage_end', methods: ['POST'])]
public function endCovoiturage(int $covoiturage_id, EntityManagerInterface $em, CovoiturageRepository $repo, EmailService $emailService): Response
{
    $covoiturage = $repo->find($covoiturage_id);

    if (!$covoiturage) {
        throw $this->createNotFoundException('Covoiturage non trouvé');
    }

    if ($covoiturage->getCreateur() !== $this->getUser()) {
        throw $this->createAccessDeniedException("Vous n'êtes pas autorisé à terminer ce covoiturage.");
    }

    $covoiturage->setStatut('termine');
    $em->flush();

    foreach ($covoiturage->getParticipants() as $participant) {
        if ($participant !== $covoiturage->getCreateur()) {
            $emailService->sendAvisInvitationEmail($participant, $covoiturage);
        }
    }

    return $this->redirectToRoute('app_utilisateur');
}

#[Route('/utilisateur/preferences', name: 'preferences', methods:['GET', 'POST'])]
public function new(Request $request, DocumentManager $dm): Response
{
        #récupère l'utilisateur connecté
        $utilisateur = $this->getUser();
        #convertir uuid en binary pour être sûr de la récupération de l'utilisateur_id
        $uuidUtilisateur = $utilisateur->getUtilisateurId()->toBinary();

        #affichage page en fonction du role métier (chauffeur, ou les 2)
        $rolesMetier = $this->roleService->getUserRolesMetier();

        if (!in_array(1, $rolesMetier)) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette page.');
        }

        $preferences = new Preference();
        $preferencesForm = $this->createForm(Preferences::class, $preferences);

        $preferencesForm->handleRequest($request);

        if ($preferencesForm->isSubmitted() && $preferencesForm->isValid()) {
            $preferences->setUtilisateurId($utilisateur->getUtilisateurId());
            $dm->persist($preferences);
            $dm->flush();

            return $this->redirectToRoute('app_utilisateur');
        }

        return $this->render('utilisateur/preferences.html.twig', [
            'preferencesForm' => $preferencesForm->createView(),
            'rolesMetier' => $rolesMetier,
        ]);
    }

    #[Route('/utilisateur/moncompte', name: 'moncompte')]
    public function monCompte(Request $request, EntityManagerInterface $entityManager): Response
    {
        #récupère l'utilisateur connecté
        $utilisateur = $this->getUser();
        #convertir uuid en binary pour être sûr de la récupération de l'utilisateur_id
        $uuidUtilisateur = $utilisateur->getUtilisateurId()->toBinary();

        #récupère role metier de l'utilisateur
        $rolesMetier = $this->roleService->getUserRolesMetier();
        
        #création formulaire
        $roleMetierForm = $this->createForm(roleMetier::class, $utilisateur);
        $roleMetierForm->handleRequest($request);

        if($roleMetierForm->isSubmitted() && $roleMetierForm->isValid()) {
            $entityManager->persist($utilisateur);
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
        ->setParameter('utilisateur', $uuidUtilisateur)
        ->getQuery()
        ->getResult();


        return $this->render('utilisateur/moncompte.html.twig', [
            'utilisateur' => $utilisateur,
            'roleMetierForm' => $roleMetierForm->createView(),
            'voiture' => $voiture,
            'rolesMetier' => $rolesMetier,
        ]);
    }


    #[Route('/utilisateur/moncompte/modification', name: 'modification')]
    public function ModifierProfil(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        #récupère utilisateur connecté
        $utilisateur = $this->getUser();

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
            

                # Générer un nom unique pour la photo
                $newFilename = uniqid() . '.' . $photo->guessExtension();

                # Déplacer le fichier dans le répertoire de stockage
                try {
                    $photo->move(
                        $this->getParameter('photos_directory'),
                        $newFilename
                    );
                    
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
        #récupère utilisateur connecté
        $utilisateur = $this->getUser();

        #affichage page en fonction du role métier (chauffeur, ou les 2)
        $rolesMetier = $this->roleService->getUserRolesMetier();

        if (!in_array(1, $rolesMetier)) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette page.');
        }

        $vehicule = new Voiture();
        $vehiculeForm = $this->createForm(Vehicule::class, $vehicule);
        $vehiculeForm->handleRequest($request);
        

        if ($vehiculeForm->isSubmitted() &&  $vehiculeForm->isValid()){
            $vehicule->setUtilisateur($utilisateur);
        
            $entityManager->persist($vehicule);
            $entityManager->flush();

            return $this->redirectToRoute('moncompte', [], response::HTTP_SEE_OTHER);
        }
    
        return $this->render('utilisateur/vehicule.html.twig', [
            'vehiculeForm' => $vehiculeForm->createView(),
            'rolesMetier' => $rolesMetier,
        ]);
    }

    #[Route('/utilisateur/proposercovoiturage', name: 'proposercovoiturage', methods:['GET', 'POST'])]
    public function proposerCovoiturage(Request $request, EntityManagerInterface $entityManager): Response
    {
        #récupère utilisateur connecté
        $utilisateur = $this->getUser();
        # Convertir l'UUID en binaire pour être sûr de la récupération de l'utilisateur_id
        $uuidUtilisateur = $utilisateur->getUtilisateurId()->toBinary();
        
        #affichage page en fonction du role métier (chauffeur, passager ou les 2)
        $rolesMetier = $this->roleService->getUserRolesMetier();

        if (!in_array(1, $rolesMetier)) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette page.');
        }

        #création formulaire
        $covoiturage = new Covoiturage();
        $covoiturage->setStatut('à venir'); #défini le statut par défaut
        $proposerForm = $this->createForm(Proposer::class, $covoiturage, [
            #Pour filtrer seulement les voitures de utilisateur
            'user' => $utilisateur
        ]);
        $proposerForm->handleRequest($request);
        #soummission formulaire et récupération données
        if ($proposerForm->isSubmitted() && $proposerForm->isValid()) {
           
            
            #Ajoute utilisateur au covoiturage (la relation ManyToMany)
            $covoiturage->addUtilisateur($utilisateur);


            #envoi nouveau covoit dans la BDD
            $this->entityManager->persist($covoiturage);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_utilisateur', [], response::HTTP_SEE_OTHER);
        }
        
        return $this->render('utilisateur/proposercovoiturage.html.twig', [
            'proposerForm' => $proposerForm->createView(),
            'rolesMetier' => $rolesMetier, #pour les transmettre à la vue
        ]);
    }
}
