<?php

namespace App\Repository;

use App\Entity\Tourner;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tourner>
 *
 * @method Tourner|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tourner|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tourner[]    findAll()
 * @method Tourner[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TournerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tourner::class);
    }

//    /**
//     * @return Tourner[] Returns an array of Tourner objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Tourner
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
