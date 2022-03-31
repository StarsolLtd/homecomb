<?php

namespace App\Tests\Unit\Service\TenancyReviewSolicitation;

use App\Entity\TenancyReviewSolicitation;
use App\Entity\User;
use App\Factory\TenancyReviewSolicitationFactory;
use App\Model\TenancyReviewSolicitation\CreateInputInterface;
use App\Service\TenancyReviewSolicitation\CreateService;
use App\Service\TenancyReviewSolicitation\SendService;
use App\Service\User\UserService;
use App\Tests\Unit\EntityManagerTrait;
use App\Tests\Unit\UserEntityFromInterfaceTrait;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

final class CreateServiceTest extends TestCase
{
    use EntityManagerTrait;
    use ProphecyTrait;
    use UserEntityFromInterfaceTrait;

    private CreateService $createService;

    private ObjectProphecy $emailService;
    private ObjectProphecy $sendService;
    private ObjectProphecy $tenancyReviewSolicitationFactory;
    private ObjectProphecy $mailer;

    public function setUp(): void
    {
        $this->sendService = $this->prophesize(SendService::class);
        $this->userService = $this->prophesize(UserService::class);
        $this->tenancyReviewSolicitationFactory = $this->prophesize(TenancyReviewSolicitationFactory::class);
        $this->entityManager = $this->prophesize(EntityManagerInterface::class);

        $this->createService = new CreateService(
            $this->sendService->reveal(),
            $this->userService->reveal(),
            $this->tenancyReviewSolicitationFactory->reveal(),
            $this->entityManager->reveal(),
        );
    }

    public function testCreateAndSend1(): void
    {
        $input = $this->prophesize(CreateInputInterface::class);
        $user = $this->prophesize(User::class);
        $tenancyReviewSolicitation = $this->prophesize(TenancyReviewSolicitation::class);

        $this->assertGetUserEntityFromInterface($user);

        $this->tenancyReviewSolicitationFactory->createEntityFromInput($input, $user)
            ->shouldBeCalledOnce()
            ->willReturn($tenancyReviewSolicitation);

        $this->assertEntitiesArePersistedAndFlush([$tenancyReviewSolicitation]);

        $this->sendService->send($tenancyReviewSolicitation, $user)->shouldBeCalledOnce();

        $output = $this->createService->createAndSend($input->reveal(), $user->reveal());

        $this->assertTrue($output->isSuccess());
    }
}
