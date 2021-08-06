<?php

namespace App\Repository;

use App\Entity\City;
use App\Entity\Property;
use App\Exception\NotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Property|null find($id, $lockMode = null, $lockVersion = null)
 * @method Property|null findOneBy(array $criteria, array $orderBy = null)
 * @method Property[]    findAll()
 * @method Property[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PropertyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Property::class);
    }

    public function findOnePublishedBySlug(string $slug): Property
    {
        $property = $this->findOneBy(
            [
                'slug' => $slug,
                'published' => true,
            ]
        );

        if (null === $property) {
            throw new NotFoundException(sprintf('No published property with slug %s could be found.', $slug));
        }

        return $property;
    }

    public function findOnePublishedById(int $id): Property
    {
        $property = $this->findOneBy(
            [
                'id' => $id,
                'published' => true,
            ]
        );

        if (null === $property) {
            throw new NotFoundException(sprintf('No published property with ID %d could be found.', $id));
        }

        return $property;
    }

    public function findOneBySlugOrNull(string $slug): ?Property
    {
        return $this->findOneBy(
            [
                'slug' => $slug,
            ]
        );
    }

    public function findOneByVendorPropertyIdOrNull(string $vendorPropertyId): ?Property
    {
        return $this->findOneBy(
            [
                'vendorPropertyId' => $vendorPropertyId,
            ]
        );
    }

    public function findOneByAddressOrNull(string $addressLine1, string $postcode): ?Property
    {
        return $this->findOneBy(
            [
                'addressLine1' => $addressLine1,
                'postcode' => $postcode,
            ]
        );
    }

    /**
     * @return ArrayCollection<int, Property>
     */
    public function findBySearchQuery(string $searchQuery, int $maxResults = 10): ArrayCollection
    {
        $results = $this->createQueryBuilder('p')
            ->where('p.addressLine1 LIKE :addressLine1Like')
            ->orWhere('p.addressLine1 = :addressLine1')
            ->orWhere('p.postcode = :postcodeLike')
            ->orWhere('p.postcode = :postcode')
            ->setParameter('addressLine1Like', $searchQuery.'%')
            ->setParameter('addressLine1', $searchQuery)
            ->setParameter('postcodeLike', $searchQuery.'%')
            ->setParameter('postcode', $searchQuery)
            ->setMaxResults($maxResults)
            ->getQuery()
            ->getResult();

        return new ArrayCollection($results);
    }

    /**
     * @return ArrayCollection<int, Property>
     */
    public function findPublishedByCity(City $city): ArrayCollection
    {
        $results = $this->findBy(
            [
                'city' => $city,
                'published' => true,
            ]
        );

        return new ArrayCollection($results);
    }
}
