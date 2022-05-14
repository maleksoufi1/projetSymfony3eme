<?php

namespace App\Repository;

use App\Entity\CategorieEvenement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CategorieEvenement|null find($id, $lockMode = null, $lockVersion = null)
 * @method CategorieEvenement|null findOneBy(array $criteria, array $orderBy = null)
 * @method CategorieEvenement[]    findAll()
 * @method CategorieEvenement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategorieEvenementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategorieEvenement::class);
    }

    public function findEntitiesByString($str){
        return $this->getEntityManager()
            ->createQuery(
                'SELECT c
                FROM APP\Entity\CategorieEvenement c
                WHERE c.libelle LIKE :str'
            )
            ->setParameter('str', '%'.$str.'%')
            ->getResult();
    }


    





    // /**
    //  * @return CategorieEvenement[] Returns an array of CategorieEvenement objects
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
    public function findOneBySomeField($value): ?CategorieEvenement
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
