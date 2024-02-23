<?php

namespace App\Repository;

use App\Entity\StatutCoursier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StatutCoursier>
 *
 * @method StatutCoursier|null find($id, $lockMode = null, $lockVersion = null)
 * @method StatutCoursier|null findOneBy(array $criteria, array $orderBy = null)
 * @method StatutCoursier[]    findAll()
 * @method StatutCoursier[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StatutCoursierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StatutCoursier::class);
    }

//    /**
//     * @return StatutCoursier[] Returns an array of StatutCoursier objects
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

//    public function findOneBySomeField($value): ?StatutCoursier
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
