<?php

namespace App\Repository;

use App\Entity\Property;
use App\Entity\TenancyReview;
use App\Exception\NotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TenancyReview|null find($id, $lockMode = null, $lockVersion = null)
 * @method TenancyReview|null findOneBy(array $criteria, array $orderBy = null)
 * @method TenancyReview[]    findAll()
 * @method TenancyReview[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TenancyReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TenancyReview::class);
    }

    public function findLastByPropertyAndAuthorOrNull(Property $property, string $author): ?TenancyReview
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

    public function findOneById(int $id): TenancyReview
    {
        $tenancyReview = $this->find($id);

        if (null === $tenancyReview) {
            throw new NotFoundException(sprintf('Review %d could be found.', $id));
        }

        return $tenancyReview;
    }

    public function findOnePublishedById(int $id): TenancyReview
    {
        $tenancyReview = $this->findOneBy(
            [
                'id' => $id,
                'published' => true,
            ]
        );

        if (null === $tenancyReview) {
            throw new NotFoundException(sprintf('Review %d could be found.', $id));
        }

        return $tenancyReview;
    }

    public function findLastPublished(): ?TenancyReview
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

    /**
     * @return TenancyReview[]
     */
    public function findLatest(int $limit = 3): array
    {
        return $this->findBy(
            [
                'published' => true,
            ],
            [
                'id' => 'DESC',
            ],
            $limit
        );
    }
}
