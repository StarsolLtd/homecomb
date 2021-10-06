<?php

namespace App\Tests\Functional\Repository;

use App\DataFixtures\TestFixtures;
use App\Entity\BroadbandProvider;
use App\Exception\NotFoundException;
use App\Repository\BroadbandProviderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class BroadbandProviderRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    private BroadbandProviderRepository $repository;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->repository = $this->entityManager->getRepository(BroadbandProvider::class);
    }

    public function testFindOnePublishedBySlug()
    {
        $broadbandProvider = $this->repository->findOnePublishedBySlug(TestFixtures::TEST_BROADBAND_PROVIDER_1_SLUG);

        $this->assertNotNull($broadbandProvider);
        $this->assertEquals(TestFixtures::TEST_BROADBAND_PROVIDER_1_SLUG, $broadbandProvider->getSlug());
        $this->assertTrue($broadbandProvider->isPublished());
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
