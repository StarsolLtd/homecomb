<?php

namespace App\Repository;

use App\Entity\LocaleReview;
use App\Exception\NotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use function sprintf;

/**
 * @method LocaleReview|null find($id, $lockMode = null, $lockVersion = null)
 * @method LocaleReview|null findOneBy(array $criteria, array $orderBy = null)
 * @method LocaleReview[]    findAll()
 * @method LocaleReview[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LocaleReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LocaleReview::class);
    }

    public function findOneById(int $id): LocaleReview
    {
        $LocaleReview = $this->find($id);

        if (null === $LocaleReview) {
            throw new NotFoundException(sprintf('LocaleReview %d could be found.', $id));
        }

        return $LocaleReview;
    }

    public function findOnePublishedById(int $id): LocaleReview
    {
        $LocaleReview = $this->findOneBy(
            [
                'id' => $id,
                'published' => true,
            ]
        );

        if (null === $LocaleReview) {
            throw new NotFoundException(sprintf('LocaleReview %d could be found.', $id));
        }

        return $LocaleReview;
    }

    public function findLastPublished(): ?LocaleReview
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
     * @return LocaleReview[]
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
