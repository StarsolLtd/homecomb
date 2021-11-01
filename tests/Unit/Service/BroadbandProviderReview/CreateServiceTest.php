<?php

namespace App\Tests\Unit\Service\BroadbandProviderReview;

use App\Entity\BroadbandProvider;
use App\Entity\BroadbandProviderReview;
use App\Entity\User;
use App\Exception\UnexpectedValueException;
use App\Factory\BroadbandProviderReviewFactory;
use App\Model\BroadbandProviderReview\SubmitInput;
use App\Model\Interaction\RequestDetails;
use App\Repository\BroadbandProviderRepository;
use App\Service\BroadbandProvider\FindOrCreateService as BroadbandProviderFindOrCreateService;
use App\Service\BroadbandProviderReview\CreateService;
use App\Service\User\UserService;
use App\Tests\Unit\EntityManagerTrait;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

final class CreateServiceTest extends TestCase
{
    use ProphecyTrait;
    use EntityManagerTrait;

    private CreateService $createService;

    private ObjectProphecy $broadbandProviderReviewFactory;
    private ObjectProphecy $findOrCreateService;
    private ObjectProphecy $broadbandProviderRepository;
    private ObjectProphecy $userService;
    private ObjectProphecy $entityManager;

    public function setUp(): void
    {
        $this->broadbandProviderReviewFactory = $this->prophesize(BroadbandProviderReviewFactory::class);
        $this->findOrCreateService = $this->prophesize(BroadbandProviderFindOrCreateService::class);
        $this->broadbandProviderRepository = $this->prophesize(BroadbandProviderRepository::class);
        $this->userService = $this->prophesize(UserService::class);
        $this->entityManager = $this->prophesize(EntityManagerInterface::class);

        $this->createService = new CreateService(
            $this->broadbandProviderReviewFactory->reveal(),
            $this->findOrCreateService->reveal(),
            $this->broadbandProviderRepository->reveal(),
            $this->userService->reveal(),
            $this->entityManager->reveal(),
        );
    }

    /**
     * Test successful review submission.
     */
    public function testSubmitReview1(): void
    {
        $requestDetails = $this->prophesize(RequestDetails::class);

        list($input, $user) = $this->prophesizeSubmitReview();

        $submitOutput = $this->createService->submitReview(
            $input->reveal(),
            $user->reveal(),
            $requestDetails->reveal()
        );

        $this->assertEquals(true, $submitOutput->isSuccess());
    }

    /**
     * Test an exception is thrown when neither a broadband provider slug nor name is supplied.
     */
    public function testSubmitReview2(): void
    {
        $requestDetails = $this->prophesize(RequestDetails::class);
        $input = $this->prophesize(SubmitInput::class);
        $user = $this->prophesize(User::class);

        $input->getBroadbandProviderSlug()->shouldBeCalledOnce()->willReturn(null);
        $input->getBroadbandProviderName()->shouldBeCalledOnce()->willReturn(null);

        $this->expectException(UnexpectedValueException::class);

        $this->createService->submitReview(
            $input->reveal(),
            $user->reveal(),
            $requestDetails->reveal()
        );
    }

    private function prophesizeSubmitReview(): array
    {
        $input = $this->prophesize(SubmitInput::class);
        $user = $this->prophesize(User::class);
        $broadbandProvider = $this->prophesize(BroadbandProvider::class);
        $broadbandProviderReview = $this->prophesize(BroadbandProviderReview::class);

        $input->getBroadbandProviderSlug()->shouldBeCalledOnce()->willReturn('test-bp-slug');

        $this->broadbandProviderRepository->findOnePublishedBySlug('test-bp-slug')->shouldBeCalledOnce()->willReturn($broadbandProvider);

        $this->userService->getUserEntityOrNullFromUserInterface($user)->shouldBeCalledOnce()->willReturn($user);

        $this->broadbandProviderReviewFactory->createEntity($input, $broadbandProvider, $user)
            ->shouldBeCalledOnce()
            ->willReturn($broadbandProviderReview)
        ;

        $this->assertEntitiesArePersistedAndFlush([$broadbandProviderReview]);

        return [$input, $user];
    }
}
