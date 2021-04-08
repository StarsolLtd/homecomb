<?php

namespace App\Tests\Repository;

use App\DataFixtures\TestFixtures;
use App\Entity\Property;
use App\Exception\NotFoundException;
use App\Repository\PropertyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PropertyRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    private PropertyRepository $repository;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->repository = $this->entityManager->getRepository(Property::class);
    }

    public function testFindOnePublishedBySlug()
    {
        $property = $this->repository->findOnePublishedBySlug(TestFixtures::TEST_PROPERTY_SLUG);

        $this->assertNotNull($property);
        $this->assertEquals(TestFixtures::TEST_PROPERTY_SLUG, $property->getSlug());
        $this->assertTrue($property->isPublished());
    }

    public function testFindOnePublishedBySlugThrowsExceptionWhenNotExists()
    {
        $this->expectException(NotFoundException::class);

        $this->repository->findOnePublishedBySlug('testnotexists');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        unset($this->entityManager, $this->repository);
    }
}
