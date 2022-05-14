<?php

namespace App\Repository;

use App\Entity\Regime;
use App\Data\FiltreData;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Regime|null find($id, $lockMode = null, $lockVersion = null)
 * @method Regime|null findOneBy(array $criteria, array $orderBy = null)
 * @method Regime[]    findAll()
 * @method Regime[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RegimeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Regime::class);
    }
    public function findListRegimeByIdUser($user_id){

        return $this->createQueryBuilder('r')
        ->Where('r.user =:user')
        ->setParameter('user',$user_id)
        ->getQuery()
        ->getResult();
    }

    public function findEntitiesByString($str){
        return $this->getEntityManager()
        ->createQuery(
            'SELECT r FROM APP\Entity\Regime r  
            WHERE r.type LIKE :str'
        )
        ->setParameter('str','%'.$str.'%')
        ->getResult();

    }
    


    public function findSearch(FiltreData $search){
        $query = $this->getSearchQuery($search)->getQuery();
            return $query; 
    }

    /**
     * return min max de la table régime correspondant a une search
     *
     * @param FiltreData $search
     * @return array
     */
    public function MinMax(FiltreData $search): array
    {
      
        $results = $this->getSearchQuery($search , true)
        ->select('MIN(r.prix) as min', 'MAX(r.prix) as max')
        ->getQuery()
        ->getScalarResult();

    return [(int)$results[0]['min'], (int)$results[0]['max']];
    }

    private function getSearchQuery(FiltreData $search, $ignorePrix = false): QueryBuilder
    {
        $query = $this
        ->createQueryBuilder('r')
        ->select('c', 'r')
        ->join('r.categorieRegime', 'c')
       ;


        if (!empty($search->q)) {
            $query = $query
                ->andWhere('r.type LIKE :q')
                ->setParameter('q', "%{$search->q}%");
        }
        if (!empty($search->min)&&($ignorePrix === false)) {
            $query = $query
                ->andWhere('r.prix >= :min')
                ->setParameter('min', $search->min);
        }

        if (!empty($search->max) && ($ignorePrix === false)) {
            $query = $query
                ->andWhere('r.prix  <= :max')
                ->setParameter('max', $search->max);
        }

        if (!empty($search->categories)) {
            $query = $query
                ->andWhere('c.id IN (:categories)')
                ->setParameter('categories', $search->categories);
        }


        return $query ; 
       
    }




     /**
     * Returns number of "Annonces" per day
     * @return void 
     */
    public function countByDate(){
        // $query = $this->createQueryBuilder('r')
        //     ->select('SUBSTRING(a.createdAt, 1, 10) as dateRegimes, COUNT(r) as count')
        //     ->groupBy('dateRegimes')
        // ;
        // return $query->getQuery()->getResult();
        $query = $this->getEntityManager()->createQuery("
            SELECT SUBSTRING(r.createdAt, 1, 10) as dateRegimes, COUNT(r) as count FROM App\Entity\Regime r GROUP BY dateRegimes ");
        return $query->getResult();
    }


    
   

    // /**
    //  * @return Regime[] Returns an array of Regime objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Regime
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
