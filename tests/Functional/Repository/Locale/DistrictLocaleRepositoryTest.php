<?php

namespace App\Tests\Functional\Repository\Locale;

use App\DataFixtures\TestFixtures;
use App\Entity\City;
use App\Entity\District;
use App\Entity\Locale\CityLocale;
use App\Entity\Locale\DistrictLocale;
use App\Repository\DistrictRepository;
use App\Repository\Locale\CityLocaleRepository;
use App\Repository\Locale\DistrictLocaleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @covers \App\Repository\Locale\DistrictLocaleRepository
 */
final class DistrictLocaleRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    private DistrictLocaleRepository $repository;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->repository = $this->entityManager->getRepository(DistrictLocale::class);
    }

    /**
     * @covers \App\Repository\Locale\DistrictLocaleRepository::findOneNullableByDistrict
     */
    public function testFindOneNullableByDistrict1(): void
    {
        /** @var DistrictRepository $districtRepository */
        $districtRepository = $this->entityManager->getRepository(District::class);

        $district = $districtRepository->findOneBySlug(TestFixtures::TEST_DISTRICT_ISLINGTON_SLUG);

        $districtLocale = $this->repository->findOneNullableByDistrict($district);

        $this->assertNotNull($districtLocale);
        $this->assertEquals('Islington', $districtLocale->getName());
        $this->assertTrue($districtLocale->isPublished());
    }

    /**
     * @covers \App\Repository\Locale\DistrictLocaleRepository::findOneNullableByDistrict
     * Test returns null when district does not have a locale in the database.
     */
    public function testFindOneNullableByDistrict2(): void
    {
        $district = (new District())->setName('Rivierenbuurt')->setCountryCode('NL');

        $districtLocale = $this->repository->findOneNullableByDistrict($district);

        $this->assertNull($districtLocale);
    }

    /**
     * @covers \App\Repository\Locale\DistrictLocaleRepository::findOneNullableByDistrict
     * Test returns null for a District that does not exist, with matching properties to a City that does exist.
     */
    public function testFindOneNullableByDistrict3(): void
    {
        /** @var CityLocaleRepository $cityLocaleRepository */
        $cityLocaleRepository = $this->entityManager->getRepository(CityLocale::class);
        $cityLocale = $cityLocaleRepository->findOneBySlug(TestFixtures::TEST_CITY_LOCALE_KINGS_LYNN_SLUG);
        $city = $cityLocale->getCity();

        $district = (new District())
            ->setName($city->getName())
            ->setCounty($city->getCountryCode())
            ->setCountryCode($city->getCountryCode())
        ;

        $districtLocale = $this->repository->findOneNullableByDistrict($district);

        $this->assertNull($districtLocale);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        unset($this->entityManager, $this->repository);
    }
}
