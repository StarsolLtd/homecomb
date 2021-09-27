<?php

namespace App\Tests\Unit\Service;

use App\Entity\Comment\Comment;
use App\Entity\Review\LocaleReview;
use App\Entity\TenancyReview;
use App\Entity\User;
use App\Entity\Vote\CommentVote;
use App\Entity\Vote\LocaleReviewVote;
use App\Entity\Vote\TenancyReviewVote;
use App\Exception\UnexpectedValueException;
use App\Factory\VoteFactory;
use App\Model\Interaction\RequestDetails;
use App\Model\Vote\SubmitInput;
use App\Model\Vote\SubmitOutput;
use App\Repository\VoteRepository;
use App\Service\InteractionService;
use App\Service\UserService;
use App\Service\VoteService;
use App\Tests\Unit\EntityManagerTrait;
use App\Tests\Unit\UserEntityFromInterfaceTrait;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @covers \App\Service\VoteService
 */
final class VoteServiceTest extends TestCase
{
    use EntityManagerTrait;
    use ProphecyTrait;
    use UserEntityFromInterfaceTrait;

    private VoteService $voteService;

    private ObjectProphecy $interactionService;
    private ObjectProphecy $voteRepository;
    private ObjectProphecy $voteFactory;

    public function setUp(): void
    {
        $this->entityManager = $this->prophesize(EntityManagerInterface::class);
        $this->interactionService = $this->prophesize(InteractionService::class);
        $this->userService = $this->prophesize(UserService::class);
        $this->voteRepository = $this->prophesize(VoteRepository::class);
        $this->voteFactory = $this->prophesize(VoteFactory::class);

        $this->voteService = new VoteService(
            $this->entityManager->reveal(),
            $this->interactionService->reveal(),
            $this->userService->reveal(),
            $this->voteRepository->reveal(),
            $this->voteFactory->reveal(),
        );
    }

    /**
     * @covers \App\Service\VoteService::vote
     * Test successful TenancyReview vote.
     */
    public function testVote1(): void
    {
        $input = $this->prophesize(SubmitInput::class);
        $output = $this->prophesize(SubmitOutput::class);
        $user = $this->prophesize(User::class);
        $vote = $this->prophesize(TenancyReviewVote::class);
        $tenancyReview = $this->prophesize(TenancyReview::class);
        $requestDetails = $this->prophesize(RequestDetails::class);

        $this->assertGetUserEntityFromInterface($user);

        $input->getEntityName()
            ->shouldBeCalledTimes(2)
            ->willReturn('TenancyReview');

        $input->getEntityId()
            ->shouldBeCalledOnce()
            ->willReturn(789);

        $this->voteRepository->findOneTenancyReviewVoteByUserAndEntity($user, 789)
            ->shouldBeCalledOnce()
            ->willReturn(null);

        $this->voteFactory->createEntityFromSubmitInput($input, $user)
            ->shouldBeCalledOnce()
            ->willReturn($vote);

        $this->assertEntitiesArePersistedAndFlush([$vote]);

        $vote->getId()->shouldBeCalledOnce()->willReturn(234);

        $this->interactionService->record('Vote', 234, $requestDetails, $user)
            ->shouldBeCalledOnce();

        $vote->getTenancyReview()->shouldBeCalledOnce()->willReturn($tenancyReview);

        $this->voteFactory->createSubmitOutputFromTenancyReview($tenancyReview)->willReturn($output);

        $this->voteService->vote($input->reveal(), $user->reveal(), $requestDetails->reveal());
    }

    /**
     * @covers \App\Service\VoteService::vote
     * Test successful Comment vote.
     */
    public function testVote2(): void
    {
        $input = $this->prophesize(SubmitInput::class);
        $output = $this->prophesize(SubmitOutput::class);
        $user = $this->prophesize(User::class);
        $vote = $this->prophesize(CommentVote::class);
        $comment = $this->prophesize(Comment::class);
        $requestDetails = $this->prophesize(RequestDetails::class);

        $this->assertGetUserEntityFromInterface($user);

        $input->getEntityName()
            ->shouldBeCalledTimes(2)
            ->willReturn('Comment');

        $input->getEntityId()
            ->shouldBeCalledOnce()
            ->willReturn(789);

        $this->voteRepository->findOneCommentVoteByUserAndEntity($user, 789)
            ->shouldBeCalledOnce()
            ->willReturn(null);

        $this->voteFactory->createEntityFromSubmitInput($input, $user)
            ->shouldBeCalledOnce()
            ->willReturn($vote);

        $this->assertEntitiesArePersistedAndFlush([$vote]);

        $vote->getId()->shouldBeCalledOnce()->willReturn(234);

        $this->interactionService->record('Vote', 234, $requestDetails, $user)
            ->shouldBeCalledOnce();

        $vote->getComment()->shouldBeCalledOnce()->willReturn($comment);

        $this->voteFactory->createSubmitOutputFromComment($comment)->willReturn($output);

        $this->voteService->vote($input->reveal(), $user->reveal(), $requestDetails->reveal());
    }

    /**
     * @covers \App\Service\VoteService::vote
     * Test successful comment vote where vote already exists.
     */
    public function testVote3(): void
    {
        $input = $this->prophesize(SubmitInput::class);
        $output = $this->prophesize(SubmitOutput::class);
        $user = $this->prophesize(User::class);
        $vote = $this->prophesize(CommentVote::class);
        $comment = $this->prophesize(Comment::class);
        $requestDetails = $this->prophesize(RequestDetails::class);

        $this->assertGetUserEntityFromInterface($user);

        $input->getEntityName()
            ->shouldBeCalledTimes(2)
            ->willReturn('Comment');

        $input->getEntityId()
            ->shouldBeCalledOnce()
            ->willReturn(789);

        $this->voteRepository->findOneCommentVoteByUserAndEntity($user, 789)
            ->shouldBeCalledOnce()
            ->willReturn($vote);

        $input->isPositive()->shouldBeCalledOnce()->willReturn(true);

        $vote->setPositive(true)->shouldBeCalledOnce();

        $this->assertFlush();

        $vote->getId()->shouldBeCalledOnce()->willReturn(234);

        $this->interactionService->record('Vote', 234, $requestDetails, $user)
            ->shouldBeCalledOnce();

        $vote->getComment()->shouldBeCalledOnce()->willReturn($comment);

        $this->voteFactory->createSubmitOutputFromComment($comment)->willReturn($output);

        $this->voteService->vote($input->reveal(), $user->reveal(), $requestDetails->reveal());
    }

    /**
     * @covers \App\Service\VoteService::vote
     * Test successful LocaleReview vote.
     */
    public function testVote4(): void
    {
        $input = $this->prophesize(SubmitInput::class);
        $output = $this->prophesize(SubmitOutput::class);
        $user = $this->prophesize(User::class);
        $vote = $this->prophesize(LocaleReviewVote::class);
        $localeReview = $this->prophesize(LocaleReview::class);
        $requestDetails = $this->prophesize(RequestDetails::class);

        $this->assertGetUserEntityFromInterface($user);

        $input->getEntityName()
            ->shouldBeCalledTimes(2)
            ->willReturn('LocaleReview');

        $input->getEntityId()
            ->shouldBeCalledOnce()
            ->willReturn(789);

        $this->voteRepository->findOneLocaleReviewVoteByUserAndEntity($user, 789)
            ->shouldBeCalledOnce()
            ->willReturn(null);

        $this->voteFactory->createEntityFromSubmitInput($input, $user)
            ->shouldBeCalledOnce()
            ->willReturn($vote);

        $this->assertEntitiesArePersistedAndFlush([$vote]);

        $vote->getId()->shouldBeCalledOnce()->willReturn(234);

        $this->interactionService->record('Vote', 234, $requestDetails, $user)
            ->shouldBeCalledOnce();

        $vote->getLocaleReview()->shouldBeCalledOnce()->willReturn($localeReview);

        $this->voteFactory->createSubmitOutputFromReview($localeReview)->willReturn($output);

        $this->voteService->vote($input->reveal(), $user->reveal(), $requestDetails->reveal());
    }

    /**
     * @covers \App\Service\VoteService::vote
     * Test returns submit output when there is no matching entity name.
     */
    public function testVote5(): void
    {
        $input = $this->prophesize(SubmitInput::class);
        $user = $this->prophesize(User::class);
        $vote = $this->prophesize(LocaleReviewVote::class);
        $requestDetails = $this->prophesize(RequestDetails::class);

        $this->assertGetUserEntityFromInterface($user);

        $input->getEntityName()
            ->shouldBeCalledTimes(2)
            ->willReturn('SomethingElse');

        $this->voteFactory->createEntityFromSubmitInput($input, $user)
            ->shouldBeCalledOnce()
            ->willReturn($vote);

        $this->assertEntitiesArePersistedAndFlush([$vote]);

        $vote->getId()->shouldBeCalledOnce()->willReturn(234);

        $this->interactionService->record('Vote', 234, $requestDetails, $user)
            ->shouldBeCalledOnce();

        $output = $this->voteService->vote($input->reveal(), $user->reveal(), $requestDetails->reveal());

        $this->assertTrue($output->isSuccess());
    }

    /**
     * @covers \App\Service\VoteService::vote
     * Test catches exception when thrown by InteractionService::record.
     */
    public function testVote6(): void
    {
        $input = $this->prophesize(SubmitInput::class);
        $output = $this->prophesize(SubmitOutput::class);
        $user = $this->prophesize(User::class);
        $vote = $this->prophesize(TenancyReviewVote::class);
        $tenancyReview = $this->prophesize(TenancyReview::class);
        $requestDetails = $this->prophesize(RequestDetails::class);

        $this->assertGetUserEntityFromInterface($user);

        $input->getEntityName()
            ->shouldBeCalledTimes(2)
            ->willReturn('TenancyReview');

        $input->getEntityId()
            ->shouldBeCalledOnce()
            ->willReturn(789);

        $this->voteRepository->findOneTenancyReviewVoteByUserAndEntity($user, 789)
            ->shouldBeCalledOnce()
            ->willReturn(null);

        $this->voteFactory->createEntityFromSubmitInput($input, $user)
            ->shouldBeCalledOnce()
            ->willReturn($vote);

        $this->assertEntitiesArePersistedAndFlush([$vote]);

        $vote->getId()->shouldBeCalledOnce()->willReturn(234);

        $this->interactionService->record('Vote', 234, $requestDetails, $user)
            ->shouldBeCalledOnce()
            ->willThrow(UnexpectedValueException::class);

        $vote->getTenancyReview()->shouldBeCalledOnce()->willReturn($tenancyReview);

        $this->voteFactory->createSubmitOutputFromTenancyReview($tenancyReview)->willReturn($output);

        $this->voteService->vote($input->reveal(), $user->reveal(), $requestDetails->reveal());
    }
}
