<?php

namespace App\Repository;

use App\Entity\Hand;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Hand|null find($id, $lockMode = null, $lockVersion = null)
 * @method Hand|null findOneBy(array $criteria, array $orderBy = null)
 * @method Hand[]    findAll()
 * @method Hand[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HandRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Hand::class);
    }

//    /**
//     * @return Hand[] Returns an array of Hand objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Hand
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
