<?php

namespace App\Repository;

use App\Entity\WhiteCard;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method WhiteCard|null find($id, $lockMode = null, $lockVersion = null)
 * @method WhiteCard|null findOneBy(array $criteria, array $orderBy = null)
 * @method WhiteCard[]    findAll()
 * @method WhiteCard[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WhiteCardRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, WhiteCard::class);
    }

//    /**
//     * @return WhiteCard[] Returns an array of WhiteCard objects
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
    public function findOneBySomeField($value): ?WhiteCard
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
