<?php

namespace App\Repository;

use App\Entity\WhiteDeck;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method WhiteDeck|null find($id, $lockMode = null, $lockVersion = null)
 * @method WhiteDeck|null findOneBy(array $criteria, array $orderBy = null)
 * @method WhiteDeck[]    findAll()
 * @method WhiteDeck[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WhiteDeckRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, WhiteDeck::class);
    }

//    /**
//     * @return WhiteDeck[] Returns an array of WhiteDeck objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?WhiteDeck
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
