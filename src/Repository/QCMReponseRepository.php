<?php

namespace App\Repository;

use App\Entity\QCMReponse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<QCMReponse>
 * 
 * @method QCMReponse|null find($id, $lockMode = null, $lockVersion = null)
 * @method QCMReponse|null findOneBy(array $criteria, array $orderBy = null)
 * @method QCMReponse[]    findAll()
 * @method QCMReponse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QCMReponseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QCMReponse::class);
    }

    // /**
    //  * @return QCMReponse[] Returns an array of QCMReponse objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('q.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?QCMReponse
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
