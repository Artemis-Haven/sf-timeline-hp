<?php

namespace App\Repository;

use App\Entity\BlackCard;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method BlackCard|null find($id, $lockMode = null, $lockVersion = null)
 * @method BlackCard|null findOneBy(array $criteria, array $orderBy = null)
 * @method BlackCard[]    findAll()
 * @method BlackCard[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlackCardRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, BlackCard::class);
    }

//    /**
//     * @return BlackCard[] Returns an array of BlackCard objects
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
    public function findOneBySomeField($value): ?BlackCard
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
