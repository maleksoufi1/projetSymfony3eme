<?php

namespace App\Repository;

use App\Entity\SuiviRegime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SuiviRegime|null find($id, $lockMode = null, $lockVersion = null)
 * @method SuiviRegime|null findOneBy(array $criteria, array $orderBy = null)
 * @method SuiviRegime[]    findAll()
 * @method SuiviRegime[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SuiviRegimeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SuiviRegime::class);
    }

    // /**
    //  * @return SuiviRegime[] Returns an array of SuiviRegime objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SuiviRegime
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */


    public function findSuiviByIdUser($user_id){

        return $this->createQueryBuilder('s')
        ->Where('s.user =:user')
        ->setParameter('user',$user_id)
        ->getQuery()
        ->getResult();
    }
    public function findListSuivisByIdRegime($regime_id){

        return $this->createQueryBuilder('s')
        ->Where('s.regime =:regime')
        ->setParameter('regime',$regime_id)
        ->getQuery()
        ->getResult();
    }
   
    
   

   
}
