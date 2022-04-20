<?php

namespace App\Tests\Functional\Repository;

use App\DataFixtures\TestFixtures;
use App\Entity\Review\Review;
use App\Exception\NotFoundException;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class ReviewRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    private ReviewRepository $repository;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->repository = $this->entityManager->getRepository(Review::class);
    }

    /**
     * @covers \App\Repository\ReviewRepository::findOnePublishedById
     * Test successfully finds a review it exists.
     */
    public function testFindOnePublishedById1(): void
    {
        $review = $this->repository->findOnePublishedBySlug(TestFixtures::TEST_REVIEW_SLUG_1);

        $this->assertNotNull($review);
        $this->assertEquals(TestFixtures::TEST_REVIEW_SLUG_1, $review->getSlug());
        $this->assertTrue($review->isPublished());
    }

    /**
     * @covers \App\Repository\ReviewRepository::findOnePublishedById
     * Test a NotFoundException is thrown when no such review exists.
     */
    public function testFindOnePublishedById2(): void
    {
        $this->expectException(NotFoundException::class);

        $this->repository->findOnePublishedBySlug('not exists');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        unset($this->entityManager, $this->repository);
    }
}
