<?php

namespace App\Tests\Unit\Service\TenancyReviewSolicitation;

use App\Entity\User;
use App\Factory\TenancyReviewSolicitationFactory;
use App\Model\TenancyReviewSolicitation\FormData;
use App\Service\TenancyReviewSolicitation\GetFormDataService;
use App\Service\User\UserService;
use App\Tests\Unit\UserEntityFromInterfaceTrait;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

final class GetFormDataServiceTest extends TestCase
{
    use ProphecyTrait;
    use UserEntityFromInterfaceTrait;

    private GetFormDataService $getFormDataService;

    private ObjectProphecy $tenancyReviewSolicitationFactory;
    private ObjectProphecy $mailer;

    public function setUp(): void
    {
        $this->userService = $this->prophesize(UserService::class);
        $this->tenancyReviewSolicitationFactory = $this->prophesize(TenancyReviewSolicitationFactory::class);

        $this->getFormDataService = new GetFormDataService(
            $this->userService->reveal(),
            $this->tenancyReviewSolicitationFactory->reveal(),
        );
    }

    public function testGetFormData1(): void
    {
        $user = $this->prophesize(User::class);
        $formData = $this->prophesize(FormData::class);

        $this->assertGetUserEntityFromInterface($user);

        $this->tenancyReviewSolicitationFactory->createFormDataModelFromUser($user)->shouldBeCalledOnce()->willReturn($formData);

        $this->getFormDataService->getFormData($user->reveal());
    }
}
