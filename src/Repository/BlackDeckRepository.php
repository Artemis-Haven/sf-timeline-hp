<?php

namespace App\Repository;

use App\Entity\BlackDeck;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method BlackDeck|null find($id, $lockMode = null, $lockVersion = null)
 * @method BlackDeck|null findOneBy(array $criteria, array $orderBy = null)
 * @method BlackDeck[]    findAll()
 * @method BlackDeck[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlackDeckRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, BlackDeck::class);
    }

//    /**
//     * @return BlackDeck[] Returns an array of BlackDeck objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BlackDeck
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
