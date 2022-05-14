<?php

namespace App\Repository;

use App\Entity\CommentaireUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CommentaireUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method CommentaireUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method CommentaireUser[]    findAll()
 * @method CommentaireUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentaireUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CommentaireUser::class);
    }

    // /**
    //  * @return CommentaireUser[] Returns an array of CommentaireUser objects
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
    public function findOneBySomeField($value): ?CommentaireUser
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
