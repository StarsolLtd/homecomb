<?php

namespace App\Repository;

use App\Entity\Property;
use App\Entity\Review;
use App\Exception\NotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use function sprintf;

/**
 * @method Review|null find($id, $lockMode = null, $lockVersion = null)
 * @method Review|null findOneBy(array $criteria, array $orderBy = null)
 * @method Review[]    findAll()
 * @method Review[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }

    public function findLastByPropertyAndAuthorOrNull(Property $property, string $author): ?Review
    {
        return $this->findOneBy(
            [
                'author' => $author,
                'property' => $property,
            ],
            [
                'id' => 'DESC',
            ]
        );
    }

    public function findOneById(int $id): Review
    {
        $review = $this->find($id);

        if (null === $review) {
            throw new NotFoundException(sprintf('Review %d could be found.', $id));
        }

        return $review;
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
            throw new NotFoundException(sprintf('Review %d could be found.', $id));
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
