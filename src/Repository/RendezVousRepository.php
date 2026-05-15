<?php

namespace App\Repository;

use App\Entity\RendezVous;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RendezVous>
 */
class RendezVousRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RendezVous::class);
    }

    //    /**
    //     * @return RendezVous[] Returns an array of RendezVous objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?RendezVous
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function findMostConsultedMedecins()
   {
    $sql = "SELECT m.nom, m.prenom, COUNT(*) AS howMany 
            FROM rendez_vous r 
            INNER JOIN medecin m ON m.id = r.medecin_id
            GROUP BY m.nom, m.prenom
            ORDER BY howMany DESC";

    $statement = $this->getEntityManager()->getConnection()->prepare($sql);
    $result = $statement->executeQuery()->fetchAllAssociative();
    return $result;
    }

    public function findMostConsultedMedecinsQb()
    { 
    return $this->createQueryBuilder('r')
        ->addSelect('m.nom, m.prenom, COUNT(r) AS howMany')
        ->join('r.medecin', 'm')
        ->groupBy('m.nom, m.prenom')
        ->orderBy('howMany', 'DESC')
        ->getQuery()
        ->getResult();
   }

   public function findMostConsultedMedecinsDql()
    {
    $query = $this->getEntityManager()->createQuery(
        'SELECT m.nom, m.prenom, COUNT(r) AS howMany
        FROM App\Entity\RendezVous r
        JOIN r.medecin m
        GROUP BY m.nom, m.prenom
        ORDER BY howMany DESC'
    );
    return $query->getResult();
    }

    public function findByMois($mois, $annee): array
{
    $debut = new \DateTime($annee . '-' . $mois . '-01');
    $fin = new \DateTime($annee . '-' . $mois . '-' . cal_days_in_month(CAL_GREGORIAN, $mois, $annee));

    return $this->createQueryBuilder('r')
        ->where('r.date >= :debut')
        ->andWhere('r.date <= :fin')
        ->setParameter('debut', $debut)
        ->setParameter('fin', $fin)
        ->orderBy('r.date', 'ASC')
        ->getQuery()
        ->getResult();
}
}