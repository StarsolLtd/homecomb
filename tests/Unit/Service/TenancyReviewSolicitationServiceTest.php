<?php

namespace App\Tests\Unit\Service;

use App\Entity\TenancyReviewSolicitation;
use App\Entity\User;
use App\Factory\TenancyReviewSolicitationFactory;
use App\Model\TenancyReviewSolicitation\CreateReviewSolicitationInput;
use App\Model\TenancyReviewSolicitation\FormData;
use App\Service\TenancyReviewSolicitation\SendService;
use App\Service\TenancyReviewSolicitationService;
use App\Service\User\UserService;
use App\Tests\Unit\EntityManagerTrait;
use App\Tests\Unit\UserEntityFromInterfaceTrait;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @covers \App\Service\TenancyReviewSolicitationService
 */
final class TenancyReviewSolicitationServiceTest extends TestCase
{
    use EntityManagerTrait;
    use ProphecyTrait;
    use UserEntityFromInterfaceTrait;

    private TenancyReviewSolicitationService $tenancyReviewSolicitationService;

    private ObjectProphecy $emailService;
    private ObjectProphecy $sendService;
    private ObjectProphecy $userService;
    private ObjectProphecy $tenancyReviewSolicitationFactory;
    private ObjectProphecy $mailer;

    public function setUp(): void
    {
        $this->sendService = $this->prophesize(SendService::class);
        $this->userService = $this->prophesize(UserService::class);
        $this->tenancyReviewSolicitationFactory = $this->prophesize(TenancyReviewSolicitationFactory::class);
        $this->entityManager = $this->prophesize(EntityManagerInterface::class);

        $this->tenancyReviewSolicitationService = new TenancyReviewSolicitationService(
            $this->sendService->reveal(),
            $this->userService->reveal(),
            $this->tenancyReviewSolicitationFactory->reveal(),
            $this->entityManager->reveal(),
        );
    }

    /**
     * @covers \App\Service\TenancyReviewSolicitationService::createAndSend
     */
    public function testCreateAndSend1(): void
    {
        $input = $this->getValidCreateReviewSolicitationInput();
        $user = $this->prophesize(User::class);

        $tenancyReviewSolicitation = $this->prophesize(TenancyReviewSolicitation::class);

        $this->assertGetUserEntityFromInterface($user);

        $this->tenancyReviewSolicitationFactory->createEntityFromInput($input, $user)
            ->shouldBeCalledOnce()
            ->willReturn($tenancyReviewSolicitation);

        $this->assertEntitiesArePersistedAndFlush([$tenancyReviewSolicitation]);

        $this->sendService->send($tenancyReviewSolicitation, $user)->shouldBeCalledOnce();

        $output = $this->tenancyReviewSolicitationService->createAndSend($input, $user->reveal());

        $this->assertTrue($output->isSuccess());
    }

    /**
     * @covers \App\Service\TenancyReviewSolicitationService::getFormData
     */
    public function testGetFormData1(): void
    {
        $user = new User();
        $formData = $this->prophesize(FormData::class);

        $this->userService->getEntityFromInterface($user)->shouldBeCalledOnce()->willReturn($user);
        $this->tenancyReviewSolicitationFactory->createFormDataModelFromUser($user)->shouldBeCalledOnce()->willReturn($formData);

        $this->tenancyReviewSolicitationService->getFormData($user);
    }

    private function getValidCreateReviewSolicitationInput(): CreateReviewSolicitationInput
    {
        return new CreateReviewSolicitationInput(
            'branchslug',
            'propertyslug',
            null,
            'Jack',
            'Harper',
            'jack.harper@starsol.co.uk',
            'SAMPLE'
        );
    }
}
