<?php

namespace App\Repository;

use App\Entity\Branch;
use App\Exception\NotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use function sprintf;

/**
 * @method Branch|null find($id, $lockMode = null, $lockVersion = null)
 * @method Branch|null findOneBy(array $criteria, array $orderBy = null)
 * @method Branch[]    findAll()
 * @method Branch[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BranchRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Branch::class);
    }

    public function findOnePublishedBySlug(string $slug): Branch
    {
        $branch = $this->findOneBy(
            [
                'slug' => $slug,
                'published' => true,
            ]
        );

        if (null === $branch) {
            throw new NotFoundException(sprintf('No published branch with slug %s could be found.', $slug));
        }

        return $branch;
    }
}
