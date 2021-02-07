<?php

namespace App\Tests\Unit\Factory;

use App\Entity\Comment\Comment;
use App\Entity\Review;
use App\Entity\User;
use App\Entity\Vote\CommentVote;
use App\Entity\Vote\ReviewVote;
use App\Exception\UnexpectedValueException;
use App\Factory\VoteFactory;
use App\Model\Vote\SubmitInput;
use App\Repository\CommentRepository;
use App\Repository\ReviewRepository;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @covers \App\Factory\voteFactory
 */
class VoteFactoryTest extends TestCase
{
    use ProphecyTrait;

    private voteFactory $voteFactory;

    private $commentRepository;
    private $reviewRepository;

    public function setUp(): void
    {
        $this->commentRepository = $this->prophesize(CommentRepository::class);
        $this->reviewRepository = $this->prophesize(ReviewRepository::class);

        $this->voteFactory = new VoteFactory(
            $this->commentRepository->reveal(),
            $this->reviewRepository->reveal(),
        );
    }

    /**
     * @covers \App\Factory\VoteFactory::createEntityFromSubmitInput
     * Test create ReviewVote
     */
    public function testCreateEntityFromSubmitInput1(): void
    {
        $input = new SubmitInput('Review', 789, false);

        $user = $this->prophesize(User::class);
        $review = $this->prophesize(Review::class);

        $this->reviewRepository->findOnePublishedById($input->getEntityId())
            ->shouldBeCalledOnce()
            ->willReturn($review);

        /** @var ReviewVote $vote */
        $vote = $this->voteFactory->createEntityFromSubmitInput($input, $user->reveal());

        $this->assertInstanceOf(ReviewVote::class, $vote);
        $this->assertEquals($review->reveal(), $vote->getReview());
        $this->assertFalse($vote->isPositive());
        $this->assertEquals($user->reveal(), $vote->getUser());
    }

    /**
     * @covers \App\Factory\VoteFactory::createEntityFromSubmitInput
     * Test create CommentVote
     */
    public function testCreateEntityFromSubmitInput2(): void
    {
        $input = new SubmitInput('Comment', 789, true);

        $user = $this->prophesize(User::class);
        $comment = $this->prophesize(Comment::class);

        $this->commentRepository->findOnePublishedById($input->getEntityId())
            ->shouldBeCalledOnce()
            ->willReturn($comment);

        /** @var CommentVote $vote */
        $vote = $this->voteFactory->createEntityFromSubmitInput($input, $user->reveal());

        $this->assertInstanceOf(CommentVote::class, $vote);
        $this->assertEquals($comment->reveal(), $vote->getComment());
        $this->assertTrue($vote->isPositive());
        $this->assertEquals($user->reveal(), $vote->getUser());
    }

    /**
     * @covers \App\Factory\VoteFactory::createEntityFromSubmitInput
     * Test throws UnexpectedValueException when entity name not supported
     */
    public function testCreateEntityFromSubmitInput3(): void
    {
        $input = new SubmitInput('Chopsticks', 789, true);

        $user = $this->prophesize(User::class);

        $this->expectException(UnexpectedValueException::class);

        $this->voteFactory->createEntityFromSubmitInput($input, $user->reveal());
    }
}
