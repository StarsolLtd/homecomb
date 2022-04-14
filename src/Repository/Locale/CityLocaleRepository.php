<?php

namespace App\Repository\Locale;

use App\Entity\City;
use App\Entity\Locale\CityLocale;
use App\Exception\NotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CityLocale|null find($id, $lockMode = null, $lockVersion = null)
 * @method CityLocale|null findOneBy(array $criteria, array $orderBy = null)
 * @method CityLocale[]    findAll()
 * @method CityLocale[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class CityLocaleRepository extends ServiceEntityRepository implements CityLocaleRepositoryInterface
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

    public function findOneBySlug(string $slug): CityLocale
    {
        $cityLocale = $this->findOneBy(
            [
                'slug' => $slug,
            ]
        );

        if (null === $cityLocale) {
            throw new NotFoundException(sprintf('No CityLocale with slug %s could be found.', $slug));
        }

        return $cityLocale;
    }
}
