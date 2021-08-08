<?php

namespace App\Tests\Unit\Factory;

use App\Entity\Comment\Comment;
use App\Entity\TenancyReview;
use App\Entity\User;
use App\Entity\Vote\CommentVote;
use App\Entity\Vote\TenancyReviewVote;
use App\Exception\UnexpectedValueException;
use App\Factory\VoteFactory;
use App\Model\Vote\SubmitInput;
use App\Repository\CommentRepository;
use App\Repository\ReviewRepository;
use App\Repository\TenancyReviewRepository;
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
    private $tenancyReviewRepository;

    public function setUp(): void
    {
        $this->commentRepository = $this->prophesize(CommentRepository::class);
        $this->reviewRepository = $this->prophesize(ReviewRepository::class);
        $this->tenancyReviewRepository = $this->prophesize(TenancyReviewRepository::class);

        $this->voteFactory = new VoteFactory(
            $this->commentRepository->reveal(),
            $this->reviewRepository->reveal(),
            $this->tenancyReviewRepository->reveal(),
        );
    }

    /**
     * @covers \App\Factory\VoteFactory::createEntityFromSubmitInput
     * Test create TenancyReviewVote
     */
    public function testCreateEntityFromSubmitInput1(): void
    {
        $input = new SubmitInput('TenancyReview', 789, false);

        $user = $this->prophesize(User::class);
        $tenancyReview = $this->prophesize(TenancyReview::class);

        $this->tenancyReviewRepository->findOnePublishedById($input->getEntityId())
            ->shouldBeCalledOnce()
            ->willReturn($tenancyReview);

        /** @var TenancyReviewVote $vote */
        $vote = $this->voteFactory->createEntityFromSubmitInput($input, $user->reveal());

        $this->assertInstanceOf(TenancyReviewVote::class, $vote);
        $this->assertEquals($tenancyReview->reveal(), $vote->getTenancyReview());
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

    /**
     * @covers \App\Factory\VoteFactory::createSubmitOutputFromTenancyReview
     */
    public function testCreateSubmitOutputFromTenancyReview1(): void
    {
        $tenancyReview = $this->prophesize(TenancyReview::class);

        $tenancyReview->getId()->shouldBeCalledOnce()->willReturn(5678);
        $tenancyReview->getPositiveVotesCount()->shouldBeCalledOnce()->willReturn(5);
        $tenancyReview->getNegativeVotesCount()->shouldBeCalledOnce()->willReturn(2);
        $tenancyReview->getVotesScore()->shouldBeCalledOnce()->willReturn(3);

        $output = $this->voteFactory->createSubmitOutputFromTenancyReview($tenancyReview->reveal());

        $this->assertTrue($output->isSuccess());
        $this->assertEquals('TenancyReview', $output->getEntityName());
        $this->assertEquals(5678, $output->getEntityId());
        $this->assertEquals(5, $output->getPositiveVotes());
        $this->assertEquals(2, $output->getNegativeVotes());
        $this->assertEquals(3, $output->getVotesScore());
    }

    /**
     * @covers \App\Factory\VoteFactory::createSubmitOutputFromComment
     */
    public function testCreateSubmitOutputFromComment1(): void
    {
        $comment = $this->prophesize(Comment::class);

        $comment->getId()->shouldBeCalledOnce()->willReturn(5678);
        $comment->getPositiveVotesCount()->shouldBeCalledOnce()->willReturn(5);
        $comment->getNegativeVotesCount()->shouldBeCalledOnce()->willReturn(2);
        $comment->getVotesScore()->shouldBeCalledOnce()->willReturn(3);

        $output = $this->voteFactory->createSubmitOutputFromComment($comment->reveal());

        $this->assertTrue($output->isSuccess());
        $this->assertEquals('Comment', $output->getEntityName());
        $this->assertEquals(5678, $output->getEntityId());
        $this->assertEquals(5, $output->getPositiveVotes());
        $this->assertEquals(2, $output->getNegativeVotes());
        $this->assertEquals(3, $output->getVotesScore());
    }
}
