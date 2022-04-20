<?php

namespace App\Repository;

use App\Entity\Agency;
use App\Exception\NotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Agency|null find($id, $lockMode = null, $lockVersion = null)
 * @method Agency|null findOneBy(array $criteria, array $orderBy = null)
 * @method Agency[]    findAll()
 * @method Agency[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class AgencyRepository extends ServiceEntityRepository implements AgencyRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Agency::class);
    }

    public function findOnePublishedBySlug(string $slug): Agency
    {
        $agency = $this->findOneBy(
            [
                'slug' => $slug,
                'published' => true,
            ]
        );

        if (null === $agency) {
            throw new NotFoundException(sprintf('No published agency with slug %s could be found.', $slug));
        }

        return $agency;
    }

    public function findOneBySlugOrNull(string $slug): ?Agency
    {
        return $this->findOneBy(
            [
                'slug' => $slug,
            ]
        );
    }

    public function findOnePublishedById(int $id): Agency
    {
        $agency = $this->findOneBy(
            [
                'id' => $id,
                'published' => true,
            ]
        );

        if (null === $agency) {
            throw new NotFoundException(sprintf('No published agency with ID %d could be found.', $id));
        }

        return $agency;
    }
}
