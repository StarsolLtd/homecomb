<?php

namespace App\Tests\Functional\Repository;

use App\Entity\City;
use App\Repository\CityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @covers \App\Repository\CityRepository
 */
final class CityRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    private CityRepository $repository;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->repository = $this->entityManager->getRepository(City::class);
    }

    /**
     * @covers \App\Repository\CityRepository::findOneByUnique
     */
    public function testFindOneByUnique1(): void
    {
        $city = $this->repository->findOneByUnique('Cambridge', 'Cambridgeshire', 'UK');

        $this->assertNotNull($city);
        $this->assertEquals('Cambridge', $city->getName());
        $this->assertTrue($city->isPublished());
    }

    /**
     * @covers \App\Repository\CityRepository::findOneByUnique
     */
    public function testFindOneByUnique2(): void
    {
        $city = $this->repository->findOneByUnique('Xanadu', 'Nonexistentshire', 'UK');

        $this->assertNull($city);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        unset($this->entityManager, $this->repository);
    }
}
