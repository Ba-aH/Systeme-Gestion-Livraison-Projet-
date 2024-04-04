<?php

namespace App\Repository;

use App\Entity\TransportationMeans;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TransportationMeans>
 *
 * @method TransportationMeans|null find($id, $lockMode = null, $lockVersion = null)
 * @method TransportationMeans|null findOneBy(array $criteria, array $orderBy = null)
 * @method TransportationMeans[]    findAll()
 * @method TransportationMeans[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransportationMeansRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TransportationMeans::class);
    }

//    /**
//     * @return TransportationMeans[] Returns an array of TransportationMeans objects
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

//    public function findOneBySomeField($value): ?TransportationMeans
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
