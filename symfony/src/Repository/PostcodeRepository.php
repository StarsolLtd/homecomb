<?php

namespace App\Repository;

use App\Entity\Postcode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Postcode|null find($id, $lockMode = null, $lockVersion = null)
 * @method Postcode|null findOneBy(array $criteria, array $orderBy = null)
 * @method Postcode[]    findAll()
 * @method Postcode[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostcodeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Postcode::class);
    }

    public function findBeginningWith(string $partialPostcode): array
    {
        dump($this->createQueryBuilder('p')
            ->where('p.postcode LIKE :postcode')
            ->orWhere('p.postcode = :postcode')
            ->setParameter('postcode', $partialPostcode.'%')
            ->getQuery()
            ->getSQL());

        return $this->createQueryBuilder('p')
            ->where('p.postcode LIKE :postcodeLike')
            ->orWhere('p.postcode = :postcode')
            ->setParameter('postcodeLike', $partialPostcode.'%')
            ->setParameter('postcode', $partialPostcode)
            ->getQuery()
            ->getResult();
    }
}
