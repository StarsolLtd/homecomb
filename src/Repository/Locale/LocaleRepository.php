<?php

namespace App\Repository\Locale;

use App\Entity\Locale\Locale;
use App\Exception\NotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Locale|null find($id, $lockMode = null, $lockVersion = null)
 * @method Locale|null findOneBy(array $criteria, array $orderBy = null)
 * @method Locale[]    findAll()
 * @method Locale[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class LocaleRepository extends ServiceEntityRepository implements LocaleRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Locale::class);
    }

    public function findOnePublishedBySlug(string $slug): Locale
    {
        $locale = $this->findOneBy(
            [
                'slug' => $slug,
                'published' => true,
            ]
        );

        if (null === $locale) {
            throw new NotFoundException(sprintf('No published locale with slug %s could be found.', $slug));
        }

        return $locale;
    }

    /**
     * @return ArrayCollection<int, Locale>
     */
    public function findBySearchQuery(string $searchQuery, int $maxResults = 10): ArrayCollection
    {
        $searchQuery = trim($searchQuery);

        $results = $this->createQueryBuilder('l')
            ->where('l.name LIKE :nameLike')
            ->orWhere('l.name = :name')
            ->setParameter('nameLike', $searchQuery.'%')
            ->setParameter('name', $searchQuery)
            ->setMaxResults($maxResults)
            ->getQuery()
            ->getResult();

        return new ArrayCollection($results);
    }
}
