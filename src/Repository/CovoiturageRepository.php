<?php

namespace App\Repository;

use App\Entity\Covoiturage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Covoiturage>
 */
class CovoiturageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Covoiturage::class);
    }

   public function searchCovoiturages(?string $LieuDepart, ?string $LieuArrivee, ?string $DateDepart, int $limit, int $offset)
    {
        $queryBuilder = $this->createQueryBuilder('c');

        if ($LieuDepart) {
            $queryBuilder->andWhere('c.lieu_depart = :LieuDepart')
                         ->setParameter('LieuDepart', $LieuDepart);
        }

        if ($LieuArrivee) {
            $queryBuilder->andWhere('c.lieu_arrivee = :LieuArrivee')
                         ->setParameter('LieuArrivee', $LieuArrivee);
        }

        if ($DateDepart) { 
            $queryBuilder->andWhere('c.date_depart = :DateDepart')
                         ->setParameter('DateDepart', $DateDepart);
        }

        #condition d'affichage = nbre de place supérieure à 0
        $queryBuilder->andWhere('c.nbre_place > 0');

        #Pagination
        $queryBuilder->setMaxResults($limit)
                     ->setFirstResult($offset);

        $results = $queryBuilder->getQuery()->getResult();

        #Total des résultats sans pagination
        $totalQuery = $this->createQueryBuilder('c')
            ->select('count(c.covoiturage_id)')
            ->where('c.lieu_depart = :LieuDepart')
            ->andWhere('c.lieu_arrivee = :LieuArrivee')
            ->andWhere('c.date_depart = :DateDepart')
            ->setParameter('LieuDepart', $LieuDepart)
            ->setParameter('LieuArrivee', $LieuArrivee)
            ->setParameter('DateDepart', $DateDepart);

        $total = $totalQuery->getQuery()->getSingleScalarResult();

        return ['results' => $results, 'total' => $total];
    }


    public function findClosestCovoiturage($LieuDepart, $LieuArrivee, $DateDepart)
    {
        $queryBuilder = $this->createQueryBuilder('c')
            ->where('c.lieu_depart = :LieuDepart')
            ->andWhere('c.lieu_arrivee = :LieuArrivee')
            ->andWhere('c.date_depart > :DateDepart')
            ->setParameter('LieuDepart', $LieuDepart)
            ->setParameter('LieuArrivee', $LieuArrivee)
            ->setParameter('DateDepart', $DateDepart)
            ->orderBy('c.date_depart', 'ASC')
            ->setMaxResults(1);

        $result = $queryBuilder->getQuery()->getOneOrNullResult();

        #Si aucun covoiturage trouvé, rechercher la date la plus proche
        if ($result === null) {
            $closestQueryBuilder = $this->createQueryBuilder('c')
                ->where('c.lieu_depart = :LieuDepart')
                ->andWhere('c.lieu_arrivee = :LieuArrivee')
                ->orderBy('c.date_depart', 'ASC')
                ->setParameter('LieuDepart', $LieuDepart)
                ->setParameter('LieuArrivee', $LieuArrivee)
                ->setMaxResults(1);
        

            $closestResult = $closestQueryBuilder->getQuery()->getOneOrNullResult();

            if ($closestResult !== null && is_object($closestResult)) {#etre sur que $closestResult est bien un objet
                #Proposer la date la plus proche, utilisation \pour ne pas importer le fichier
                $dateDepart = \DateTime::createFromFormat('d/m/Y', $closestResult->getDateDepart());
                return [
                    'message' => 'Aucun covoiturage trouvé pour la date spécifiée. La date la plus proche disponible est : ' . $dateDepart->format('d/m/Y'),
                    'result' => $closestResult
                ];
            } else {
                // Aucun covoiturage trouvé
                return [
                    'message' => 'Aucun covoiturage trouvé pour les lieux spécifiés.',
                    'result' => null
                    ];
                }
            }

            return [
                'message' => 'Covoiturage trouvé pour la date spécifiée.',
                'result' => $result
            ];
    }


    public function findWithFilters(?string $LieuDepart, ?string $LieuArrivee, ?string $DateDepart,?string $prix, ?string $heure): array
    {
        $qb = $this->createQueryBuilder('c');

        // Filtrer par lieu de départ
        if ($LieuDepart) {
            $qb->andWhere('c.lieu_depart = :LieuDepart')
                ->setParameter('LieuDepart', $LieuDepart);
        }

        // Filtrer par lieu d'arrivée
        if ($LieuArrivee) {
            $qb->andWhere('c.lieu_arrivee = :LieuArrivee')
                ->setParameter('LieuArrivee', $LieuArrivee);
        }

        // Filtrer par date de départ
        if ($DateDepart) {
            $dateTime= \DateTime::createFromFormat('d/m/Y', $DateDepart);
            if ($dateTime)  {
                $qb->andWhere('c.date_depart = :DateDepart')
                ->setParameter('DateDepart', $dateTime);
            }
            
        }

        if ($prix === 'moins5') {
            $qb->andWhere('c.prix_personne < 5');
        } elseif ($prix === '5_10') {
            $qb->andWhere('c.prix_personne BETWEEN 5 AND 10');
        } elseif ($prix === 'plus10') {
            $qb->andWhere('c.prix_personne > 10');
        }

        if ($heure === 'matin') {
            $qb->andWhere('c.heure_depart < :midi')
             ->setParameter('midi', new \DateTime('12:00'));
        } elseif ($heure === 'apresmidi') {
            $qb->andWhere('c.heure_depart >= :midi')
            ->setParameter('midi', new \DateTime('12:00'));
        }

        return $qb->getQuery()->getResult();
    }

}

