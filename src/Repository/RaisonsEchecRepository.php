<?php

namespace App\Repository;

use App\Entity\RaisonsEchec;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RaisonsEchec>
 *
 * @method RaisonsEchec|null find($id, $lockMode = null, $lockVersion = null)
 * @method RaisonsEchec|null findOneBy(array $criteria, array $orderBy = null)
 * @method RaisonsEchec[]    findAll()
 * @method RaisonsEchec[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RaisonsEchecRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RaisonsEchec::class);
    }

//    /**
//     * @return RaisonsEchec[] Returns an array of RaisonsEchec objects
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

//    public function findOneBySomeField($value): ?RaisonsEchec
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
