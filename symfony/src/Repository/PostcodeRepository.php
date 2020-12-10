<?php

namespace App\Repository;

use App\Entity\Postcode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    /**
     * @return Collection<int, Postcode>
     */
    public function findBeginningWith(string $partialPostcode): Collection
    {
        $results = $this->createQueryBuilder('p')
            ->where('p.postcode LIKE :postcodeLike')
            ->orWhere('p.postcode = :postcode')
            ->setParameter('postcodeLike', $partialPostcode.'%')
            ->setParameter('postcode', $partialPostcode)
            ->getQuery()
            ->getResult();

        return new ArrayCollection($results);
    }
}
