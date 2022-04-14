<?php

namespace App\Repository;

use App\Entity\District;
use App\Exception\NotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method District|null find($id, $lockMode = null, $lockVersion = null)
 * @method District|null findOneBy(array $criteria, array $orderBy = null)
 * @method District[]    findAll()
 * @method District[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class DistrictRepository extends ServiceEntityRepository implements DistrictRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, District::class);
    }

    public function findOneByUnique(string $district, ?string $county, string $countryCode): ?District
    {
        return $this->findOneBy(
            [
                'name' => $district,
                'county' => $county,
                'countryCode' => $countryCode,
            ]
        );
    }

    public function findOneBySlug(string $slug): District
    {
        $district = $this->findOneBy(
            [
                'slug' => $slug,
            ]
        );

        if (null === $district) {
            throw new NotFoundException(sprintf('No district with slug %s could be found.', $slug));
        }

        return $district;
    }
}
