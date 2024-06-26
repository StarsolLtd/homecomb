<?php

namespace App\Repository;

use App\Entity\Agency;
use App\Entity\Branch;
use App\Entity\User;
use App\Exception\NotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use LogicException;

/**
 * @method Branch|null find($id, $lockMode = null, $lockVersion = null)
 * @method Branch|null findOneBy(array $criteria, array $orderBy = null)
 * @method Branch[]    findAll()
 * @method Branch[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class BranchRepository extends ServiceEntityRepository implements BranchRepositoryInterface
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

    public function findOnePublishedById(int $id): Branch
    {
        $branch = $this->findOneBy(
            [
                'id' => $id,
                'published' => true,
            ]
        );

        if (null === $branch) {
            throw new NotFoundException(sprintf('No published branch with ID %d could be found.', $id));
        }

        return $branch;
    }

    public function findOneBySlugUserCanManage(string $slug, User $user): Branch
    {
        $agency = $user->getAdminAgency();
        if (null === $agency) {
            throw new LogicException(sprintf('User %s is not an agency admin.', $user->getUsername()));
        }

        $branch = $this->findOneBy(
            [
                'slug' => $slug,
                'agency' => $agency,
            ]
        );

        if (null === $branch) {
            throw new NotFoundException(sprintf('No branch with slug %s could be found for user %s.', $slug, $user->getUsername()));
        }

        return $branch;
    }

    public function findOneByNameAndAgencyOrNull(string $name, Agency $agency): ?Branch
    {
        return $this->findOneBy(
            [
                'name' => $name,
                'agency' => $agency,
            ]
        );
    }

    public function findOneByNameWithoutAgencyOrNull(string $name): ?Branch
    {
        return $this->findOneBy(
            [
                'name' => $name,
                'agency' => null,
            ]
        );
    }
}
