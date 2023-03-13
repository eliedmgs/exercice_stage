<?php

namespace App\Repository;

use App\Entity\QCMQuestion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<QCMQuestion>
 * 
 * @method QCMQuestion|null find($id, $lockMode = null, $lockVersion = null)
 * @method QCMQuestion|null findOneBy(array $criteria, array $orderBy = null)
 * @method QCMQuestion[]    findAll()
 * @method QCMQuestion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QCMQuestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QCMQuestion::class);
    }

    // /**
    //  * @return QCMQuestion[] Returns an array of QCMQuestion objects
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
    public function findOneBySomeField($value): ?QCMQuestion
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
