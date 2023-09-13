<?php

namespace App\Repository;

use App\Entity\UrlEntry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UrlEntry>
 *
 * @method UrlEntry|null find($id, $lockMode = null, $lockVersion = null)
 * @method UrlEntry|null findOneBy(array $criteria, array $orderBy = null)
 * @method UrlEntry[]    findAll()
 * @method UrlEntry[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UrlEntryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UrlEntry::class);
    }

    public function save(UrlEntry $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);
 
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
 
    public function remove(UrlEntry $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);
 
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return UrlEntry[] Returns an array of UrlEntry objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?UrlEntry
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
