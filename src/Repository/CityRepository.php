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
class CityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, City::class);
    }

    public function findOnePublishedByUnique(string $city, string $county, string $country): City
    {
        $city = $this->findOneBy(
            [
                'name' => $city,
                'county' => $county,
                'country' => $country,
                'published' => true,
            ]
        );

        if (null === $city) {
            throw new NotFoundException(sprintf('No published City with name %s could be found.', $city));
        }

        return $city;
    }
}
