<?php

namespace App\Repository;

use App\Entity\Review\Review;
use App\Exception\NotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Review|null find($id, $lockMode = null, $lockVersion = null)
 * @method Review|null findOneBy(array $criteria, array $orderBy = null)
 * @method Review[]    findAll()
 * @method Review[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class ReviewRepository extends ServiceEntityRepository implements ReviewRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }

    public function findOnePublishedById(int $id): Review
    {
        $review = $this->findOneBy(
            [
                'id' => $id,
                'published' => true,
            ]
        );

        if (null === $review) {
            throw new NotFoundException(sprintf('Review %d could not be found.', $id));
        }

        return $review;
    }

    public function findOnePublishedBySlug(string $slug): Review
    {
        $review = $this->findOneBy(
            [
                'slug' => $slug,
                'published' => true,
            ]
        );

        if (null === $review) {
            throw new NotFoundException(sprintf('Review %s could not be found.', $slug));
        }

        return $review;
    }

    public function findLastPublished(): ?Review
    {
        return $this->findOneBy(
            [
                'published' => true,
            ],
            [
                'id' => 'DESC',
            ]
        );
    }
}
