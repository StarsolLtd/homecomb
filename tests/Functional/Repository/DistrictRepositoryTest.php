<?php

namespace App\Tests\Functional\Repository;

use App\Entity\District;
use App\Repository\DistrictRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @covers \App\Repository\DistrictRepository
 */
final class DistrictRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    private DistrictRepository $repository;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->repository = $this->entityManager->getRepository(District::class);
    }

    /**
     * @covers \App\Repository\DistrictRepository::findOneByUnique
     */
    public function testFindOneByUnique1()
    {
        $district = $this->repository->findOneByUnique('East Cambridgeshire', 'Cambridgeshire', 'UK');

        $this->assertNotNull($district);
        $this->assertEquals('East Cambridgeshire', $district->getName());
        $this->assertTrue($district->isPublished());
    }

    /**
     * @covers \App\Repository\DistrictRepository::findOneByUnique
     */
    public function testFindOneByUnique2()
    {
        $district = $this->repository->findOneByUnique('Xanadu', 'Nonexistentshire', 'UK');

        $this->assertNull($district);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        unset($this->entityManager, $this->repository);
    }
}
