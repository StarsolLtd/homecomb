<?php

namespace App\Repository;

use App\Entity\City;
use App\Entity\Locale\CityLocale;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CityLocale|null find($id, $lockMode = null, $lockVersion = null)
 * @method CityLocale|null findOneBy(array $criteria, array $orderBy = null)
 * @method CityLocale[]    findAll()
 * @method CityLocale[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CityLocaleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CityLocale::class);
    }

    public function findOneNullableByCity(City $city): ?CityLocale
    {
        $qb = $this->createQueryBuilder('l');
        $qb->where($qb->expr()->isInstanceOf('l', CityLocale::class))
            ->andWhere('l.city = :city')
            ->setMaxResults(1)
            ->setParameter('city', $city)
        ;

        $query = $qb->getQuery();

        $result = $query->getResult();

        return $result[0] ?? null;
    }
}
