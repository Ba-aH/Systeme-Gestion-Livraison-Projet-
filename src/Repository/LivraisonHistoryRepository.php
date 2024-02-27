<?php

namespace App\Repository;

use App\Entity\LivraisonHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LivraisonHistory>
 *
 * @method LivraisonHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method LivraisonHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method LivraisonHistory[]    findAll()
 * @method LivraisonHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LivraisonHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LivraisonHistory::class);
    }

//    /**
//     * @return LivraisonHistory[] Returns an array of LivraisonHistory objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?LivraisonHistory
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
