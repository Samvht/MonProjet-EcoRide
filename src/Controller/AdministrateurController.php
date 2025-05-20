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
    public function dashboard(EntityManagerInterface $entityManager, Request $request): Response
    {
        // Vérifie que l'utilisateur a le rôle d'administrateur
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        #Récupére le nombre de covoiturages par jour
        $conn = $entityManager->getConnection();
        #transformer string en format date, pour récupérer date et données s'affichent
        #STR_TO_DATE convertit chaine DD/MM/YY en objet date
        #DATE_FORMAT met les fates au bon format YYYY-MM-DD
        $sql = '
            SELECT DATE_FORMAT(STR_TO_DATE(date_depart, "%d/%m/%Y"), "%Y-%m-%d") AS date, COUNT(*) AS count
            FROM covoiturage
            GROUP BY DATE_FORMAT(STR_TO_DATE(date_depart, "%d/%m/%Y"), "%Y-%m-%d")
            ORDER BY DATE_FORMAT(STR_TO_DATE(date_depart, "%d/%m/%Y"), "%Y-%m-%d")
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

        #Obtenir le numéro de page à partir des paramètres de requête (par défaut 1)
        $page = $request->query->getInt('page', 1);

        #Définir le nombre d'utilisateurs par page, 85 sur la 1ère et 10 celle d'après
        $usersPerPage = ($page === 1) ? 5 : 10;

        #Calculer l'offset (le point de départ des résultats)
        $offset = ($page === 1) ? 0 : (5 + ($page - 2) * 10);

        #Récupérer les utilisateurs avec limite et offset
        $query = $entityManager->getRepository(Utilisateur::class)
            ->createQueryBuilder('u')
            ->setFirstResult($offset)
            ->setMaxResults($usersPerPage)
            ->getQuery();

        $utilisateurs = $query->getResult();

        #Calculer le nombre total d'utilisateurs pour la pagination
        $totalUsers = $entityManager->getRepository(Utilisateur::class)
            ->createQueryBuilder('u')
            ->select('count(u.utilisateur_id)')
            ->getQuery()
            ->getSingleScalarResult();

        #Calculer le nombre total de pages
        $totalPages = ceil(($totalUsers - 5) / 10) + 1;



        return $this->render('administrateur/administrateur.html.twig', [
            'labels' => $labels,
            'data' => $data,
            'utilisateurs' => $utilisateurs,
            'currentPage' => $page,
            'totalPages' => $totalPages
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
