<?php

namespace App\Repository;

use App\Entity\RaisonSignalement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RaisonSignalement>
 *
 * @method RaisonSignalement|null find($id, $lockMode = null, $lockVersion = null)
 * @method RaisonSignalement|null findOneBy(array $criteria, array $orderBy = null)
 * @method RaisonSignalement[]    findAll()
 * @method RaisonSignalement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RaisonSignalementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RaisonSignalement::class);
    }

//    /**
//     * @return RaisonSignalement[] Returns an array of RaisonSignalement objects
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

//    public function findOneBySomeField($value): ?RaisonSignalement
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
