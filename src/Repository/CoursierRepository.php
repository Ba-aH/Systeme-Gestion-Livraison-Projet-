<?php

namespace App\Repository;

use App\Entity\Coursier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Coursier>
 *
 * @method Coursier|null find($id, $lockMode = null, $lockVersion = null)
 * @method Coursier|null findOneBy(array $criteria, array $orderBy = null)
 * @method Coursier[]    findAll()
 * @method Coursier[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CoursierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Coursier::class);
    }

//    /**
//     * @return Coursier[] Returns an array of Coursier objects
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

//    public function findOneBySomeField($value): ?Coursier
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
