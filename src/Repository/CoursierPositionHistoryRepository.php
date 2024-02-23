<?php

namespace App\Repository;

use App\Entity\CoursierPositionHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CoursierPositionHistory>
 *
 * @method CoursierPositionHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method CoursierPositionHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method CoursierPositionHistory[]    findAll()
 * @method CoursierPositionHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CoursierPositionHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CoursierPositionHistory::class);
    }

//    /**
//     * @return CoursierPositionHistory[] Returns an array of CoursierPositionHistory objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?CoursierPositionHistory
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
