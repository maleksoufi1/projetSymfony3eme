<?php

namespace App\Repository;

use App\Entity\AchatBillet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AchatBillet|null find($id, $lockMode = null, $lockVersion = null)
 * @method AchatBillet|null findOneBy(array $criteria, array $orderBy = null)
 * @method AchatBillet[]    findAll()
 * @method AchatBillet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AchatBilletRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AchatBillet::class);
    }

    // /**
    //  * @return AchatBillet[] Returns an array of AchatBillet objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AchatBillet
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
