<?php

namespace App\Tests\Unit\Service;

use App\Entity\User;
use App\Entity\Vote\ReviewVote;
use App\Exception\UnexpectedValueException;
use App\Factory\VoteFactory;
use App\Model\Interaction\RequestDetails;
use App\Model\Vote\SubmitInput;
use App\Service\InteractionService;
use App\Service\UserService;
use App\Service\VoteService;
use App\Tests\Unit\EntityManagerTrait;
use App\Tests\Unit\UserEntityFromInterfaceTrait;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @covers \App\Service\VoteService
 */
class VoteServiceTest extends TestCase
{
    use EntityManagerTrait;
    use ProphecyTrait;
    use UserEntityFromInterfaceTrait;

    private VoteService $voteService;

    private $entityManager;
    private $interactionService;
    private $userService;
    private $voteFactory;

    public function setUp(): void
    {
        $this->entityManager = $this->prophesize(EntityManagerInterface::class);
        $this->interactionService = $this->prophesize(InteractionService::class);
        $this->userService = $this->prophesize(UserService::class);
        $this->voteFactory = $this->prophesize(VoteFactory::class);

        $this->voteService = new VoteService(
            $this->entityManager->reveal(),
            $this->interactionService->reveal(),
            $this->userService->reveal(),
            $this->voteFactory->reveal(),
        );
    }

    /**
     * @covers \App\Service\VoteService::vote
     * Test success.
     */
    public function testVote1(): void
    {
        $input = $this->prophesize(SubmitInput::class);
        $user = $this->prophesize(User::class);
        $vote = $this->prophesize(ReviewVote::class);
        $requestDetails = $this->prophesize(RequestDetails::class);

        $this->assertGetUserEntityFromInterface($user);

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
    public function testVote2(): void
    {
        $input = $this->prophesize(SubmitInput::class);
        $user = $this->prophesize(User::class);
        $vote = $this->prophesize(ReviewVote::class);
        $requestDetails = $this->prophesize(RequestDetails::class);

        $this->assertGetUserEntityFromInterface($user);

        $this->voteFactory->createEntityFromSubmitInput($input, $user)
            ->shouldBeCalledOnce()
            ->willReturn($vote);

        $this->assertEntitiesArePersistedAndFlush([$vote]);

        $vote->getId()->shouldBeCalledOnce()->willReturn(234);

        $this->interactionService->record('Vote', 234, $requestDetails, $user)
            ->shouldBeCalledOnce()
            ->willThrow(UnexpectedValueException::class);

        $output = $this->voteService->vote($input->reveal(), $user->reveal(), $requestDetails->reveal());

        $this->assertTrue($output->isSuccess());
    }
}
