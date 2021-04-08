<?php

namespace App\Tests\Functional\Repository;

use App\DataFixtures\TestFixtures;
use App\Entity\Comment\Comment;
use App\Entity\Review;
use App\Entity\User;
use App\Entity\Vote\CommentVote;
use App\Entity\Vote\ReviewVote;
use App\Entity\Vote\Vote;
use App\Repository\CommentRepository;
use App\Repository\ReviewRepository;
use App\Repository\UserRepository;
use App\Repository\VoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @covers \App\Repository\VoteRepository
 */
class VoteRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    private VoteRepository $voteRepository;

    private CommentRepository $commentRepository;
    private ReviewRepository $reviewRepository;
    private UserRepository $userRepository;

    private ?Comment $commentFixture;
    private ?Review $reviewFixture;
    private ?User $userFixture;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->voteRepository = $this->entityManager->getRepository(Vote::class);

        $this->commentRepository = $this->entityManager->getRepository(Comment::class);
        $this->reviewRepository = $this->entityManager->getRepository(Review::class);
        $this->userRepository = $this->entityManager->getRepository(User::class);

        $this->createFixtures();
    }

    /**
     * @covers \App\Repository\VoteRepository::findOneReviewVoteByUserAndEntity
     */
    public function testFindOneReviewVoteByUserAndEntity1()
    {
        $vote = $this->voteRepository->findOneReviewVoteByUserAndEntity($this->userFixture, $this->reviewFixture->getId());

        $this->assertNotNull($vote);
        $this->assertEquals($vote->getReview(), $this->reviewFixture);
        $this->assertEquals($vote->getUser(), $this->userFixture);
        $this->assertTrue($vote->isPositive());
    }

    /**
     * @covers \App\Repository\VoteRepository::findOneCommentVoteByUserAndEntity
     */
    public function testFindOneCommentVoteByUserAndEntity1()
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

    private function createFixtures()
    {
        $this->commentFixture = $this->commentRepository->findLastPublished();
        $this->reviewFixture = $this->reviewRepository->findLastPublished();
        $this->userFixture = $this->userRepository->loadUserByUsername(TestFixtures::TEST_USER_STANDARD_EMAIL);

        $reviewVoteEntity = (new ReviewVote())->setUser($this->userFixture)->setReview($this->reviewFixture)->setPositive(true);
        $this->entityManager->persist($reviewVoteEntity);

        $commentVoteEntity = (new CommentVote())->setUser($this->userFixture)->setComment($this->commentFixture)->setPositive(false);
        $this->entityManager->persist($commentVoteEntity);

        $this->entityManager->flush();
    }
}
