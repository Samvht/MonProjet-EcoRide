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
            #Si l'utilisateur est déjà connecté, rediriger vers page en fonction role
            $roles = $this->getUser()->getRoles();
            if (in_array('ROLE_ADMIN', $roles)) {
                return $this->redirectToRoute('administrateur'); // Redirige vers la page administrateur
            } else {
                return $this->redirectToRoute('app_utilisateur'); // Redirige vers la page utilisateur
            }
        }

        # Récupère l'erreur de connexion s'il y en a une / ne peux utiliser json car formulaire action 
        $error = $authenticationUtils->getLastAuthenticationError();
       
        $utilisateurConnexion = new Utilisateur();
        $connexionForm = $this->createForm(Connexion::class, $utilisateurConnexion); 
        
        $utilisateurInscription = new Utilisateur();
        $inscriptionForm = $this->createForm(Inscription::class, $utilisateurInscription); 
        

        #retourne la vue
        return $this->render('connexion/connexion.html.twig', [
            'connexionForm' => $connexionForm->createView(),
            'inscriptionForm'=> $inscriptionForm->createView(),
            'error' => $error,
            'controller_name' => 'ConnexionController',
        ]);
    }

    #Route pour action d'inscription pour éviter qu'il prenne le formulaire connexion automatiquement
    #[Route('/inscription', name: 'inscription')]
    public function inscription(Request $request, AuthenticationUtils $authenticationUtils) : Response
    { 
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
                    return $this->json(['erreur' => 'Cet email est déjà utilisé.'], 400);
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

        return $this->json(['erreur' => 'Une erreur est survenue.'], 400); #celui là conseillé, à modifier

        #return new Response('{ "erreur": "Je ne sais pas" }', 400); # à supprimer si utilise celui du haut
     }


    #Fonction pour connecter l'utilisateur manuellement après l'inscription
    private function loginUser(Utilisateur $utilisateur): void {
        #crée un token identification utilisateur
        $token = new UsernamePasswordToken($utilisateur, "main", $utilisateur->getRoles());
        #Stocke le token dans la session
        $this->tokenStorage->setToken($token);
        $this->requestStack->getCurrentRequest()->getSession()->set('_security_main', serialize($token));
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout()
    {
    #deconnection géré par symfony directement, target rentré dans security.yaml
    return $this->redirectToRoute('app_connexion');
    }
}