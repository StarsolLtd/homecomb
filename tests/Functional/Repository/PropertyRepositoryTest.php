<?php

namespace App\Tests\Functional\Repository;

use App\DataFixtures\TestFixtures;
use App\Entity\City;
use App\Entity\Property;
use App\Exception\NotFoundException;
use App\Repository\PropertyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @covers \App\Repository\PropertyRepository
 */
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
        $property = $this->repository->findOnePublishedBySlug(TestFixtures::TEST_PROPERTY_1_SLUG);

        $this->assertNotNull($property);
        $this->assertEquals(TestFixtures::TEST_PROPERTY_1_SLUG, $property->getSlug());
        $this->assertTrue($property->isPublished());
    }

    public function testFindOnePublishedBySlugThrowsExceptionWhenNotExists()
    {
        $this->expectException(NotFoundException::class);

        $this->repository->findOnePublishedBySlug('testnotexists');
    }

    /**
     * Test one result is found when there should only be one result. Found by addressLine1.
     */
    public function testFindBySearchQuery1()
    {
        $properties = $this->repository->findBySearchQuery('Tester');
        $this->assertCount(1, $properties);
        $property = $properties->first();
        $this->assertEquals(TestFixtures::TEST_PROPERTY_1_SLUG, $property->getSlug());
    }

    /**
     * Test multiple properties are found when there are multiple results. Found by postcode.
     */
    public function testFindBySearchQuery2()
    {
        $properties = $this->repository->findBySearchQuery('PE31 8RP');
        $this->assertCount(3, $properties);
        $this->assertEquals(TestFixtures::TEST_PROPERTY_2_SLUG, $properties[0]->getSlug());
        $this->assertEquals(TestFixtures::TEST_PROPERTY_3_SLUG, $properties[1]->getSlug());
        $this->assertEquals(TestFixtures::TEST_PROPERTY_4_SLUG, $properties[2]->getSlug());
    }

    /**
     * Test results set is limited when it exceed $maxResults.
     */
    public function testFindBySearchQuery3()
    {
        $properties = $this->repository->findBySearchQuery('PE31 8RP', 2);
        $this->assertCount(2, $properties);
    }

    /**
     * Test results set is empty when there should be no results.
     */
    public function testFindBySearchQuery4()
    {
        $properties = $this->repository->findBySearchQuery('The Clangers Moon Base');
        $this->assertCount(0, $properties);
    }

    /**
     * @covers \App\Repository\PropertyRepository::testFindPublishedByCity1
     */
    public function testFindPublishedByCity1()
    {
        $cityRepository = $this->entityManager->getRepository(City::class);
        $city = $cityRepository->findOneBy(['name' => "King's Lynn"]);

        $properties = $this->repository->findPublishedByCity($city);
        $this->assertCount(3, $properties);
        $property = $properties->first();
        $this->assertEquals('Callisto Cottage', $property->getAddressLine1());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        unset($this->entityManager, $this->repository);
    }
}
