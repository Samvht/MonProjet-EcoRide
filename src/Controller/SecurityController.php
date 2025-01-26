<?php

namespace App\Controller;

use App\Form\Connexion;
use App\Entity\Utilisateur;
use App\Entity\Configuration;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Form\Inscription;

class SecurityController extends AbstractController
{
    private $entityManager;
    private $passwordHasher;
    private $tokenStorage;


    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, TokenStorageInterface $tokenStorage)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->tokenStorage = $tokenStorage;
    }

    #[Route(path: '/connexion', name: 'app_connexion')]
    public function login(Request $request, AuthenticationUtils $authenticationUtils): Response
    {
        #Si utilisateur déjà connecté redirection vers /utilisateur
        if ($this->getUser()) {
            return $this->redirectToRoute('target_path');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        #Formulaire ConnexionForm
        $utilisateurConnexion = new Utilisateur();
        $connexionForm = $this->createForm(Connexion::class, $utilisateurConnexion); 
        $connexionForm->handleRequest($request); 
        if ($connexionForm->isSubmitted() && $connexionForm->isValid()) { 
            #récupération données formulaire
            $utilisateurConnexion = $connexionForm->getData();
            $email = $utilisateurConnexion->getEmail(); 
            $password = $utilisateurConnexion->getPassword();
        
             #Verifier si l'utilisateur existe
             $utilisateurConnexion = $this->entityManager->getRepository(Utilisateur::class)->findOneBy(['email' => $email]);
             if ($utilisateurConnexion && $this->passwordHasher->isPasswordValid($utilisateurConnexion, $password)) {
                $token = new UsernamePasswordToken(
                    $utilisateurConnexion, 
                    $password, 
                    ['main'], 
                    $utilisateurConnexion->getRoles());
                $this->tokenStorage->setToken($token);
                $request->getSession()->set('_security_main', serialize($token));
                $this->addFlash('success', 'Connexion réussie.');  
             
             return $this->redirectToRoute('app_utilisateur', [], response::HTTP_SEE_OTHER); 
            } else {
             #Message si identifiants incorrect
             $this->addFlash('error', 'Identifiants incorrects');
            }
        }

        #Formulaire inscription
        $utilisateurInscription = new Utilisateur();
        $inscriptionForm = $this->createForm(Inscription::class, $utilisateurInscription); 
        $inscriptionForm->handleRequest($request); 

        
        if ($inscriptionForm->isSubmitted() && $inscriptionForm->isValid()) {
            // Récupération des données du formulaire d'inscription
            $utilisateurInscription = $inscriptionForm->getData(); 
            $pseudo = $utilisateurInscription->getPseudo(); 
            $email = $utilisateurInscription->getEmail(); 
            $password = $utilisateurInscription->getPassword();

            // Vérifier si l'email existe déjà
            $existingUser = $this->entityManager->getRepository(Utilisateur::class)->findOneBy(['email' => $email]);
            if ($existingUser) {
                $this->addFlash('error', 'Cet email est déjà utilisé.');
            } else {
                #créer un nouvel utilisateur et hash le mot de passe
                $utilisateurInscription->setPseudo($pseudo);
                $utilisateurInscription->setEmail($email);
                $encodedPassword = $this->passwordHasher->hashPassword($utilisateurInscription, $password); 
                $utilisateurInscription->setPassword($encodedPassword);

                #Définit la config utilisateur part défaut
                $configuration = $this->entityManager->getRepository(Configuration::class)->findOneBy(['name' => 'ROLE_USER']);
                if (!$configuration) {
                    $configuration = new Configuration();
                    $configuration->setName('ROLE_USER');
                    $this->entityManager->persist($configuration);
                    $this->entityManager->flush();}
                $utilisateurInscription->setConfiguration($configuration);

                #créer nouvel utilisateur et l'envoyer dans la BDD
                $this->entityManager->persist($utilisateurInscription);
                $this->entityManager->flush();

                # Authentification automatique après l'inscription
                $token = new UsernamePasswordToken($utilisateurInscription, $password, ['main'], $utilisateurInscription->getRoles());
                $this->tokenStorage->setToken($token);
                $request->getSession()->set('_security_main', serialize($token));
            return $this->redirectToRoute('app_utilisateur', [], response::HTTP_SEE_OTHER); 
            } 
        }


        return $this->render('connexion/connexion.html.twig', [
            'last_username' => $lastUsername, 
            'error' => $error,
            'connexionForm' => $connexionForm->createView(),
            'inscriptionForm'=> $inscriptionForm->createView(),
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
