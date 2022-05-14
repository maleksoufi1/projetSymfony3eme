<?php

namespace App\Repository;

use App\Entity\Evenement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Evenement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Evenement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Evenement[]    findAll()
 * @method Evenement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EvenementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Evenement::class);
    }
    
    public function findByIdCategorie($id){

        return $this->createQueryBuilder('e')
        ->Where('e.categorieEvenement =:categorieEvenement')
        ->setParameter('categorieEvenement',$id)
        ->getQuery()
        ->getResult();
    }
    public function findByIddetail($id){

        return $this->createQueryBuilder('e')
        ->Where('e.id =:id')
        ->setParameter('id',$id)
        ->getQuery()
        ->getResult();
    }
    

    public function findEntitiesByStringe($str){
        return $this->getEntityManager()
            ->createQuery(
                'SELECT e
                FROM APP\Entity\Evenement e
                WHERE e.titre LIKE :str'
            )
            ->setParameter('str', '%'.$str.'%')
            ->getResult();
    }


    // /**
    //  * @return Evenement[] Returns an array of Evenement objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Evenement
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
