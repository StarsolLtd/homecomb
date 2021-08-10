<?php

namespace App\Repository\Locale;

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
        return $this->findOneBy([
            'city' => $city,
        ]);
    }
}
