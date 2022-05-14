<?php

namespace App\Repository;

use App\Entity\CreateProgramme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CreateProgramme|null find($id, $lockMode = null, $lockVersion = null)
 * @method CreateProgramme|null findOneBy(array $criteria, array $orderBy = null)
 * @method CreateProgramme[]    findAll()
 * @method CreateProgramme[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CreateProgrammeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CreateProgramme::class);
    }

    // /**
    //  * @return CreateProgramme[] Returns an array of CreateProgramme objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CreateProgramme
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
