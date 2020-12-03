<?php

namespace App\Repository;

use App\Entity\Agency;
use App\Exception\NotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use function sprintf;

/**
 * @method Agency|null find($id, $lockMode = null, $lockVersion = null)
 * @method Agency|null findOneBy(array $criteria, array $orderBy = null)
 * @method Agency[]    findAll()
 * @method Agency[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AgencyRepository extends ServiceEntityRepository
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
}
