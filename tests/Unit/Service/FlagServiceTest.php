<?php

namespace App\Tests\Unit\Service;

use App\Entity\Flag\TenancyReviewFlag;
use App\Entity\User;
use App\Factory\FlagFactory;
use App\Model\Flag\SubmitInput;
use App\Model\Interaction\RequestDetails;
use App\Service\FlagService;
use App\Service\InteractionService;
use App\Service\NotificationService;
use App\Service\User\UserService;
use App\Tests\Unit\EntityManagerTrait;
use App\Tests\Unit\UserEntityFromInterfaceTrait;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @covers \App\Service\FlagService
 */
final class FlagServiceTest extends TestCase
{
    use EntityManagerTrait;
    use ProphecyTrait;
    use UserEntityFromInterfaceTrait;

    private FlagService $flagService;

    private ObjectProphecy $interactionService;
    private ObjectProphecy $notificationService;
    private ObjectProphecy $flagFactory;

    public function setUp(): void
    {
        $this->entityManager = $this->prophesize(EntityManagerInterface::class);
        $this->interactionService = $this->prophesize(InteractionService::class);
        $this->notificationService = $this->prophesize(NotificationService::class);
        $this->userService = $this->prophesize(UserService::class);
        $this->flagFactory = $this->prophesize(FlagFactory::class);

        $this->flagService = new FlagService(
            $this->entityManager->reveal(),
            $this->interactionService->reveal(),
            $this->notificationService->reveal(),
            $this->userService->reveal(),
            $this->flagFactory->reveal(),
        );
    }

    /**
     * @covers \App\Service\FlagService::submitFlag
     * Test success with valid review data.
     */
    public function testSubmitFlag1(): void
    {
        $input = $this->prophesize(SubmitInput::class);
        $user = $this->prophesize(User::class);
        $flag = $this->prophesize(TenancyReviewFlag::class);
        $requestDetails = $this->prophesize(RequestDetails::class);

        $this->assertGetUserEntityOrNullFromInterface($user);

        $this->flagFactory->createEntityFromSubmitInput($input, $user)
            ->shouldBeCalledOnce()
            ->willReturn($flag);

        $this->assertEntitiesArePersistedAndFlush([$flag]);

        $this->notificationService->sendFlagModerationNotification($flag)->shouldBeCalledOnce();

        $flag->getId()->shouldBeCalledOnce()->willReturn(234);

        $this->interactionService->record(InteractionService::TYPE_FLAG, 234, $requestDetails, $user)
            ->shouldBeCalledOnce();

        $output = $this->flagService->submitFlag($input->reveal(), $user->reveal(), $requestDetails->reveal());

        $this->assertTrue($output->isSuccess());
    }
}
