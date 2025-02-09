<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Utilisateur;
use Symfony\Component\HttpFoundation\Request;

class AdministrateurController extends AbstractController
{
    #[Route('/administrateur', name: 'administrateur')]
    public function dashboard(EntityManagerInterface $entityManager): Response
    {
        // Vérifie que l'utilisateur a le rôle d'administrateur
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        #Récupération utilisateurs pour les afficher
        $utilisateurs = $entityManager->getRepository(Utilisateur::class)->findAll();

        #Récupére le nombre de covoiturages par jour
        $conn = $entityManager->getConnection();
        $sql = '
            SELECT DATE(date_depart) AS date, COUNT(*) AS count
            FROM covoiturage
            GROUP BY DATE(date_depart)
            ORDER BY DATE(date_depart)
        ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        $covoituragesParJour = $resultSet->fetchAllAssociative();

        #Préparer les labels et les données pour le graphique
        $labels = []; #date
        $data = []; #nbre covoiturage
        foreach ($covoituragesParJour as $row) {
            $labels[] = $row['date'];
            $data[] = $row['count'];
        }



        return $this->render('administrateur/administrateur.html.twig', [
            'utilisateurs' => $utilisateurs,
            'labels' => $labels,
            'data' => $data
        ]);
    }

    #[Route('/administrateur/suspendre/{utilisateur_id}', name: 'admin_suspendre_utilisateur', methods: ['POST'])]
    public function suspendre(int $utilisateur_id, EntityManagerInterface $entityManager, Request $request): Response
    {
        #Vérifie que l'utilisateur a le rôle d'administrateur
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        #récupération utilisateur pour affichage
        $utilisateur = $entityManager->getRepository(Utilisateur::class)->find($utilisateur_id);

        if (!$utilisateur) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        #Vérification du token CSRF
        if (!$this->isCsrfTokenValid('suspendre' . $utilisateur->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('administrateur');
        }

        #Suspendre l'utilisateur (par exemple, en ajoutant un rôle "ROLE_SUSPENDU")
        $utilisateur->setRoles(['ROLE_SUSPENDU']);
        $entityManager->flush();

        $this->addFlash('success', 'Compte utilisateur suspendu avec succès.');

        return $this->redirectToRoute('administrateur');
    }
}
