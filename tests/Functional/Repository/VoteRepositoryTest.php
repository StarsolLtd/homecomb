<?php

namespace App\Tests\Functional\Repository;

use App\DataFixtures\TestFixtures;
use App\Entity\Comment\Comment;
use App\Entity\TenancyReview;
use App\Entity\User;
use App\Entity\Vote\CommentVote;
use App\Entity\Vote\TenancyReviewVote;
use App\Entity\Vote\Vote;
use App\Repository\CommentRepository;
use App\Repository\TenancyReviewRepository;
use App\Repository\UserRepository;
use App\Repository\VoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @covers \App\Repository\VoteRepository
 */
final class VoteRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    private VoteRepository $voteRepository;

    private CommentRepository $commentRepository;
    private TenancyReviewRepository $reviewRepository;
    private UserRepository $userRepository;

    private ?Comment $commentFixture;
    private ?TenancyReview $tenancyReviewFixture;
    private ?User $userFixture;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->voteRepository = $this->entityManager->getRepository(Vote::class);

        $this->commentRepository = $this->entityManager->getRepository(Comment::class);
        $this->reviewRepository = $this->entityManager->getRepository(TenancyReview::class);
        $this->userRepository = $this->entityManager->getRepository(User::class);

        $this->createFixtures();
    }

    /**
     * @covers \App\Repository\VoteRepository::findOneTenancyReviewVoteByUserAndEntity
     */
    public function testFindOneReviewVoteByUserAndEntity1(): void
    {
        $vote = $this->voteRepository->findOneTenancyReviewVoteByUserAndEntity($this->userFixture, $this->tenancyReviewFixture->getId());

        $this->assertNotNull($vote);
        $this->assertEquals($vote->getTenancyReview(), $this->tenancyReviewFixture);
        $this->assertEquals($vote->getUser(), $this->userFixture);
        $this->assertTrue($vote->isPositive());
    }

    /**
     * @covers \App\Repository\VoteRepository::findOneCommentVoteByUserAndEntity
     */
    public function testFindOneCommentVoteByUserAndEntity1(): void
    {
        $vote = $this->voteRepository->findOneCommentVoteByUserAndEntity($this->userFixture, $this->commentFixture->getId());

        $this->assertNotNull($vote);
        $this->assertEquals($vote->getComment(), $this->commentFixture);
        $this->assertEquals($vote->getUser(), $this->userFixture);
        $this->assertFalse($vote->isPositive());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        unset($this->entityManager, $this->voteRepository);
    }

    private function createFixtures(): void
    {
        $this->commentFixture = $this->commentRepository->findLastPublished();
        $this->tenancyReviewFixture = $this->reviewRepository->findLastPublished();
        $this->userFixture = $this->userRepository->loadUserByUsername(TestFixtures::TEST_USER_STANDARD_1_EMAIL);

        $reviewVoteEntity = (new TenancyReviewVote())->setUser($this->userFixture)->setTenancyReview($this->tenancyReviewFixture)->setPositive(true);
        $this->entityManager->persist($reviewVoteEntity);

        $commentVoteEntity = (new CommentVote())->setUser($this->userFixture)->setComment($this->commentFixture)->setPositive(false);
        $this->entityManager->persist($commentVoteEntity);

        $this->entityManager->flush();
    }
}
