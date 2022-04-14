<?php

namespace App\Tests\Unit\Factory;

use App\Entity\Comment\Comment;
use App\Entity\Review\LocaleReview;
use App\Entity\TenancyReview;
use App\Entity\User;
use App\Entity\Vote\CommentVote;
use App\Entity\Vote\LocaleReviewVote;
use App\Entity\Vote\TenancyReviewVote;
use App\Exception\UnexpectedValueException;
use App\Factory\VoteFactory;
use App\Model\Vote\SubmitInputInterface;
use App\Repository\CommentRepositoryInterface;
use App\Repository\ReviewRepositoryInterface;
use App\Repository\TenancyReviewRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @covers \App\Factory\voteFactory
 */
final class VoteFactoryTest extends TestCase
{
    use ProphecyTrait;

    private VoteFactory $voteFactory;

    private ObjectProphecy $commentRepository;
    private ObjectProphecy $reviewRepository;
    private ObjectProphecy $tenancyReviewRepository;

    public function setUp(): void
    {
        $this->commentRepository = $this->prophesize(CommentRepositoryInterface::class);
        $this->reviewRepository = $this->prophesize(ReviewRepositoryInterface::class);
        $this->tenancyReviewRepository = $this->prophesize(TenancyReviewRepositoryInterface::class);

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
        $input = $this->prophesize(SubmitInputInterface::class);
        $input->getEntityName()->shouldBeCalledOnce()->willReturn('TenancyReview');
        $input->getEntityId()->shouldBeCalledOnce()->willReturn(789);
        $input->isPositive()->shouldBeCalledOnce()->willReturn(false);

        $user = $this->prophesize(User::class);
        $tenancyReview = $this->prophesize(TenancyReview::class);

        $this->tenancyReviewRepository->findOnePublishedById(789)
            ->shouldBeCalledOnce()
            ->willReturn($tenancyReview);

        /** @var TenancyReviewVote $vote */
        $vote = $this->voteFactory->createEntityFromSubmitInput($input->reveal(), $user->reveal());

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
        $input = $this->prophesize(SubmitInputInterface::class);
        $input->getEntityName()->shouldBeCalledOnce()->willReturn('Comment');
        $input->getEntityId()->shouldBeCalledOnce()->willReturn(789);
        $input->isPositive()->shouldBeCalledOnce()->willReturn(true);

        $user = $this->prophesize(User::class);
        $comment = $this->prophesize(Comment::class);

        $this->commentRepository->findOnePublishedById(789)
            ->shouldBeCalledOnce()
            ->willReturn($comment);

        /** @var CommentVote $vote */
        $vote = $this->voteFactory->createEntityFromSubmitInput($input->reveal(), $user->reveal());

        $this->assertInstanceOf(CommentVote::class, $vote);
        $this->assertEquals($comment->reveal(), $vote->getComment());
        $this->assertTrue($vote->isPositive());
        $this->assertEquals($user->reveal(), $vote->getUser());
    }

    /**
     * @covers \App\Factory\VoteFactory::createEntityFromSubmitInput
     * Test create LocaleReviewVote
     */
    public function testCreateEntityFromSubmitInput3(): void
    {
        $input = $this->prophesize(SubmitInputInterface::class);
        $input->getEntityName()->shouldBeCalledOnce()->willReturn('LocaleReview');
        $input->getEntityId()->shouldBeCalledOnce()->willReturn(789);
        $input->isPositive()->shouldBeCalledOnce()->willReturn(true);

        $user = $this->prophesize(User::class);
        $review = $this->prophesize(LocaleReview::class);

        $this->reviewRepository->findOnePublishedById(789)
            ->shouldBeCalledOnce()
            ->willReturn($review);

        /** @var LocaleReviewVote $vote */
        $vote = $this->voteFactory->createEntityFromSubmitInput($input->reveal(), $user->reveal());

        $this->assertInstanceOf(LocaleReviewVote::class, $vote);
        $this->assertEquals($review->reveal(), $vote->getLocaleReview());
        $this->assertTrue($vote->isPositive());
        $this->assertEquals($user->reveal(), $vote->getUser());
    }

    /**
     * @covers \App\Factory\VoteFactory::createEntityFromSubmitInput
     * Test throws UnexpectedValueException when entity name not supported
     */
    public function testCreateEntityFromSubmitInput4(): void
    {
        $input = $this->prophesize(SubmitInputInterface::class);
        $input->getEntityName()->shouldBeCalledOnce()->willReturn('Chopsticks');
        $input->getEntityId()->shouldBeCalledOnce()->willReturn(789);

        $user = $this->prophesize(User::class);

        $this->expectException(UnexpectedValueException::class);

        $this->voteFactory->createEntityFromSubmitInput($input->reveal(), $user->reveal());
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
     * @covers \App\Factory\VoteFactory::createSubmitOutputFromReview
     */
    public function testCreateSubmitOutputFromReview1(): void
    {
        $review = $this->prophesize(LocaleReview::class);

        $review->getId()->shouldBeCalledOnce()->willReturn(5678);
        $review->getPositiveVotesCount()->shouldBeCalledOnce()->willReturn(5);
        $review->getNegativeVotesCount()->shouldBeCalledOnce()->willReturn(2);
        $review->getVotesScore()->shouldBeCalledOnce()->willReturn(3);

        $output = $this->voteFactory->createSubmitOutputFromReview($review->reveal());

        $this->assertTrue($output->isSuccess());
        $this->assertEquals('Review', $output->getEntityName());
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
