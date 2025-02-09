<?php

namespace App\Controller;


use App\Entity\Utilisateur;
use App\Entity\Configuration;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\SecurityBundle\Security as security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Psr\Log\LoggerInterface;
use App\Form\Connexion;
use App\Form\Inscription;


class ConnexionController extends AbstractController
{
    private $entityManager;
    private $passwordHasher;
    private $tokenStorage;
    private $requestStack;
    private LoggerInterface $logger;


    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, TokenStorageInterface $tokenStorage, RequestStack $requestStack, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->tokenStorage = $tokenStorage;
        $this->requestStack = $requestStack;
        $this->logger = $logger;
        
    }

    #[Route('/connexion', name: 'app_connexion')]
    public function connexion(Request $request, AuthenticationUtils $authenticationUtils) : Response
    {
        if ($this->getUser()) {
            #Si l'utilisateur est déjà connecté, rediriger vers page utilisateur
            return $this->redirectToRoute('app_utilisateur'); 
        }
       
        $utilisateurConnexion = new Utilisateur();
        $connexionForm = $this->createForm(Connexion::class, $utilisateurConnexion); 
        /*$connexionForm->handleRequest($request); 
        if ($connexionForm->isSubmitted() && $connexionForm->isValid()) { 
            #récupération données formulaire
            $utilisateurConnexion = $connexionForm->getData();
            $email = $utilisateurConnexion->getEmail(); 
            $password = $utilisateurConnexion->getPassword();*/
        
             #Verifier si l'utilisateur existe - enlever car gérer par connexion_check et la magie de symfony
             /*$utilisateurConnexion = $this->entityManager->getRepository(Utilisateur::class)->findOneBy(['email' => $email]);
             if ($utilisateurConnexion && $this->passwordHasher->isPasswordValid($utilisateurConnexion, $password)) {
                $token = new UsernamePasswordToken(
                    $utilisateurConnexion, 
                    $password, 
                    ['main'], 
                    $utilisateurConnexion->getRoles());
                $this->tokenStorage->setToken($token);
                dump($this->getUser());
                $this->addFlash('success', 'Connexion réussie.');  
             
             return $this->redirectToRoute('app_utilisateur', [], response::HTTP_SEE_OTHER); 
            } else {
             #Message si identifiants incorrect
             $this->addFlash('error', 'Identifiants incorrects');
            }
        }*/
        
    
        $utilisateurInscription = new Utilisateur();
        $inscriptionForm = $this->createForm(Inscription::class, $utilisateurInscription); 
        $inscriptionForm->handleRequest($request); 

        
        if ($inscriptionForm->isSubmitted() && $inscriptionForm->isValid()) {
            #Récupération des données du formulaire d'inscription
            $utilisateurInscription = $inscriptionForm->getData(); 
            $pseudo = $utilisateurInscription->getPseudo(); 
            $email = $utilisateurInscription->getEmail(); 
            $password = $utilisateurInscription->getPassword();

            #Vérifier si l'email existe déjà
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

            #Authentifier l'utilisateur après l'inscription
            $this->loginUser($utilisateurInscription);

            return $this->redirectToRoute('app_utilisateur', [], response::HTTP_SEE_OTHER); 
            } 
        }
    
    

        #retourne la vue
        return $this->render('connexion/connexion.html.twig', [
            'connexionForm' => $connexionForm->createView(),
            'inscriptionForm'=> $inscriptionForm->createView(),
            'controller_name' => 'ConnexionController',
        ]);
    }

    #Fonction pour connecter l'utilisateur manuellement après l'inscription
    private function loginUser(Utilisateur $utilisateur): void
        {
       
        #crée un token identification utilisateur
        $token = new UsernamePasswordToken($utilisateur, "", ['main'], $utilisateur->getRoles());
        #Stocke le token dans la session
        $this->tokenStorage->setToken($token);
        $this->requestStack->getCurrentRequest()->getSession()->set('_security_main', serialize($token));
    }

    /*#[Route('/app_connexion_check', name: 'app_connexion_check')]
    public function connexionCheck() : Response
    {
        return $this->redirectToRoute('app_utilisateur');
    }*/


    #[Route('/logout', name: 'app_logout')]
    public function logout()
    {
    #deconnection géré par symfony directement, target rentré dans security.yaml
    return $this->redirectToRoute('app_connexion');
    }
}