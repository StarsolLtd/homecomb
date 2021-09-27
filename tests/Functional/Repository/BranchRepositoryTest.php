<?php

namespace App\Tests\Repository;

use App\DataFixtures\TestFixtures;
use App\Entity\Branch;
use App\Exception\NotFoundException;
use App\Repository\BranchRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class BranchRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    private BranchRepository $repository;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->repository = $this->entityManager->getRepository(Branch::class);
    }

    public function testFindOnePublishedBySlug()
    {
        $branch = $this->repository->findOnePublishedBySlug(TestFixtures::TEST_BRANCH_101_SLUG);

        $this->assertNotNull($branch);
        $this->assertEquals(TestFixtures::TEST_BRANCH_101_SLUG, $branch->getSlug());
        $this->assertTrue($branch->isPublished());
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
