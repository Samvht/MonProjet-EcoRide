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

   public function searchCovoiturages(?string $LieuDepart, ?string $LieuArrivee, ?string $DateDepart)
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

    

        return $queryBuilder->getQuery()->getResult();
    }

//    public function findOneBySomeField($value): ?Covoiturage
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
