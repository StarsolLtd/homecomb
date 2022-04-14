<?php

namespace App\Repository;

use App\Entity\BroadbandProvider;
use App\Exception\NotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BroadbandProvider|null find($id, $lockMode = null, $lockVersion = null)
 * @method BroadbandProvider|null findOneBy(array $criteria, array $orderBy = null)
 * @method BroadbandProvider[]    findAll()
 * @method BroadbandProvider[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class BroadbandProviderRepository extends ServiceEntityRepository implements BroadbandProviderRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BroadbandProvider::class);
    }

    public function findOnePublishedBySlug(string $slug): BroadbandProvider
    {
        $BroadbandProvider = $this->findOneBy(
            [
                'slug' => $slug,
                'published' => true,
            ]
        );

        if (null === $BroadbandProvider) {
            throw new NotFoundException(sprintf('No published BroadbandProvider with slug %s could be found.', $slug));
        }

        return $BroadbandProvider;
    }
}
