<?php

namespace App\Repository;

use App\Entity\City;
use App\Exception\NotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method City|null find($id, $lockMode = null, $lockVersion = null)
 * @method City|null findOneBy(array $criteria, array $orderBy = null)
 * @method City[]    findAll()
 * @method City[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class CityRepository extends ServiceEntityRepository implements CityRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, City::class);
    }

    public function findOneByUnique(string $city, ?string $county, string $countryCode): ?City
    {
        return $this->findOneBy(
            [
                'name' => $city,
                'county' => $county,
                'countryCode' => $countryCode,
            ]
        );
    }

    public function findOneBySlug(string $slug): City
    {
        $city = $this->findOneBy(
            [
                'slug' => $slug,
            ]
        );

        if (null === $city) {
            throw new NotFoundException(sprintf('No city with slug %s could be found.', $slug));
        }

        return $city;
    }
}
