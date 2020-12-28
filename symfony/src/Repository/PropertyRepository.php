<?php

namespace App\Repository;

use App\Entity\Property;
use App\Exception\NotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use function sprintf;

/**
 * @method Property|null find($id, $lockMode = null, $lockVersion = null)
 * @method Property|null findOneBy(array $criteria, array $orderBy = null)
 * @method Property[]    findAll()
 * @method Property[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PropertyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Property::class);
    }

    public function findOnePublishedBySlug(string $slug): Property
    {
        $property = $this->findOneBy(
            [
                'slug' => $slug,
                'published' => true,
            ]
        );

        if (null === $property) {
            throw new NotFoundException(sprintf('No published property with slug %s could be found.', $slug));
        }

        return $property;
    }

    public function findOneBySlugOrNull(string $slug): ?Property
    {
        return $this->findOneBy(
            [
                'slug' => $slug,
            ]
        );
    }
}
