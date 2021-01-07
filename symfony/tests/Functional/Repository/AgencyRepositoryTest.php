<?php

namespace App\Tests\Repository;

use App\DataFixtures\TestFixtures;
use App\Entity\Agency;
use App\Exception\NotFoundException;
use App\Repository\AgencyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AgencyRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    private AgencyRepository $repository;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->repository = $this->entityManager->getRepository(Agency::class);
    }

    public function testFindOnePublishedBySlug()
    {
        $agency = $this->repository->findOnePublishedBySlug(TestFixtures::TEST_AGENCY_1_SLUG);

        $this->assertNotNull($agency);
        $this->assertEquals(TestFixtures::TEST_AGENCY_1_SLUG, $agency->getSlug());
        $this->assertTrue($agency->isPublished());
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
