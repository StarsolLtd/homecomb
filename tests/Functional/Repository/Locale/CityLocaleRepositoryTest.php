<?php

namespace App\Tests\Functional\Repository\Locale;

use App\DataFixtures\TestFixtures;
use App\Entity\City;
use App\Entity\Locale\CityLocale;
use App\Repository\CityRepository;
use App\Repository\Locale\CityLocaleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @covers \App\Repository\Locale\CityLocaleRepository
 */
final class CityLocaleRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    private CityLocaleRepository $repository;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->repository = $this->entityManager->getRepository(CityLocale::class);
    }

    /**
     * @covers \App\Repository\Locale\CityLocaleRepository::findOneNullableByCity
     */
    public function testFindOneNullableByCity1(): void
    {
        /** @var CityRepository $cityRepository */
        $cityRepository = $this->entityManager->getRepository(City::class);

        $city = $cityRepository->findOneBySlug(TestFixtures::TEST_CITY_KINGS_LYNN_SLUG);

        $cityLocale = $this->repository->findOneNullableByCity($city);

        $this->assertNotNull($cityLocale);
        $this->assertEquals("King's Lynn", $cityLocale->getName());
        $this->assertTrue($cityLocale->isPublished());
    }

    /**
     * @covers \App\Repository\Locale\CityLocaleRepository::findOneNullableByCity
     * Test returns null when city does not have a locale in the database.
     */
    public function testFindOneNullableByCity2(): void
    {
        $city = (new City())->setName('Amsterdam')->setCountryCode('NL');

        $cityLocale = $this->repository->findOneNullableByCity($city);

        $this->assertNull($cityLocale);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        unset($this->entityManager, $this->repository);
    }
}
