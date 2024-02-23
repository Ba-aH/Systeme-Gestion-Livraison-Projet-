<?php

namespace App\Repository;

use App\Entity\StatutLivraison;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StatutLivraison>
 *
 * @method StatutLivraison|null find($id, $lockMode = null, $lockVersion = null)
 * @method StatutLivraison|null findOneBy(array $criteria, array $orderBy = null)
 * @method StatutLivraison[]    findAll()
 * @method StatutLivraison[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StatutLivraisonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StatutLivraison::class);
    }

//    /**
//     * @return StatutLivraison[] Returns an array of StatutLivraison objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?StatutLivraison
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
