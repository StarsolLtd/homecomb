<?php

namespace App\Repository\Locale;

use App\Entity\District;
use App\Entity\Locale\DistrictLocale;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DistrictLocale|null find($id, $lockMode = null, $lockVersion = null)
 * @method DistrictLocale|null findOneBy(array $criteria, array $orderBy = null)
 * @method DistrictLocale[]    findAll()
 * @method DistrictLocale[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DistrictLocaleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DistrictLocale::class);
    }

    public function findOneNullableByDistrict(District $district): ?DistrictLocale
    {
        return $this->findOneBy([
            'district' => $district,
        ]);
    }
}
