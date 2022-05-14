<?php

namespace App\Repository;

use App\Entity\Programme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Programme|null find($id, $lockMode = null, $lockVersion = null)
 * @method Programme|null findOneBy(array $criteria, array $orderBy = null)
 * @method Programme[]    findAll()
 * @method Programme[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProgrammeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Programme::class);
    }

  
    public function findProgrammesByCat($categorie_programme_id ){

        return $this->createQueryBuilder('p')
        ->Where('p.categorieProgramme =:categorieProgramme')
        ->setParameter('categorieProgramme',$categorie_programme_id)
        ->getQuery()
        ->getResult();
    }

     /**
     * get all posts
     *
     * @return array
     */
    public function findAllPosts()
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT a
         FROM App:Programme a   
      
         ORDER BY a.id DESC'
            )
            ->getArrayResult();
    }
    

    public function findEntitiesByString($str){
        return $this->getEntityManager()
            ->createQuery(
                'SELECT p
                FROM App:Programme p
                WHERE p.titre LIKE :str OR p.difficulte LIKE :str OR p.id LIKE :str  OR p.affiche LIKE :str OR p.type LIKE :str  OR p.description LIKE :str'
            )
            ->setParameter('str', '%'.$str.'%')
            ->getResult();
    }
    
}
