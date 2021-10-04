<?php

namespace App\Tests\Unit\Service;

use App\Entity\Agency;
use App\Entity\Branch;
use App\Entity\Locale\Locale;
use App\Entity\Postcode;
use App\Entity\Property;
use App\Entity\TenancyReview;
use App\Entity\User;
use App\Exception\UnexpectedValueException;
use App\Factory\TenancyReviewFactory;
use App\Model\Interaction\RequestDetails;
use App\Model\TenancyReview\SubmitInput;
use App\Repository\PostcodeRepository;
use App\Repository\PropertyRepository;
use App\Service\Agency\FindOrCreateService as AgencyFindOrCreateService;
use App\Service\Branch\BranchFindOrCreateService;
use App\Service\InteractionService;
use App\Service\NotificationService;
use App\Service\TenancyReviewService;
use App\Service\TenancyReviewSolicitation\CompleteService;
use App\Service\User\UserService;
use App\Tests\Unit\EntityManagerTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @covers \App\Service\TenancyReviewService
 */
final class TenancyReviewServiceTest extends TestCase
{
    use ProphecyTrait;
    use EntityManagerTrait;

    private TenancyReviewService $tenancyReviewService;

    private ObjectProphecy $agencyFindOrCreateService;
    private ObjectProphecy $branchFindOrCreateService;
    private ObjectProphecy $interactionService;
    private ObjectProphecy $notificationService;
    private ObjectProphecy $completeService;
    private ObjectProphecy $userService;
    private ObjectProphecy $entityManager;
    private ObjectProphecy $postcodeRepository;
    private ObjectProphecy $propertyRepository;
    private ObjectProphecy $tenancyReviewFactory;

    public function setUp(): void
    {
        $this->agencyFindOrCreateService = $this->prophesize(AgencyFindOrCreateService::class);
        $this->branchFindOrCreateService = $this->prophesize(BranchFindOrCreateService::class);
        $this->interactionService = $this->prophesize(InteractionService::class);
        $this->notificationService = $this->prophesize(NotificationService::class);
        $this->completeService = $this->prophesize(CompleteService::class);
        $this->userService = $this->prophesize(UserService::class);
        $this->entityManager = $this->prophesize(EntityManagerInterface::class);
        $this->postcodeRepository = $this->prophesize(PostcodeRepository::class);
        $this->propertyRepository = $this->prophesize(PropertyRepository::class);
        $this->tenancyReviewFactory = $this->prophesize(TenancyReviewFactory::class);

        $this->tenancyReviewService = new TenancyReviewService(
            $this->agencyFindOrCreateService->reveal(),
            $this->branchFindOrCreateService->reveal(),
            $this->interactionService->reveal(),
            $this->notificationService->reveal(),
            $this->completeService->reveal(),
            $this->userService->reveal(),
            $this->entityManager->reveal(),
            $this->postcodeRepository->reveal(),
            $this->propertyRepository->reveal(),
            $this->tenancyReviewFactory->reveal(),
        );
    }

    /**
     * @covers \App\Service\TenancyReviewService::generateLocales
     */
    public function testGenerateLocales1(): void
    {
        $property = (new Property())->setPostcode('NR2 4SF');
        $tenancyReview = (new TenancyReview())->setProperty($property);
        $postcode = (new Postcode())->setPostcode('NR2');
        $postcodeCollection = (new ArrayCollection());
        $postcodeCollection->add($postcode);
        $locale = (new Locale())->setName('Norwich')->addPostcode($postcode);

        $this->postcodeRepository
            ->findBeginningWith('NR2')
            ->shouldBeCalledOnce()
            ->willReturn($postcodeCollection);

        $this->entityManager
            ->flush()
            ->shouldBeCalledOnce();

        $locales = $this->tenancyReviewService->generateLocales($tenancyReview);

        $this->assertEquals($locale, $locales->first());
    }

    /**
     * @covers \App\Service\TenancyReviewService::generateLocales
     */
    public function testGenerateLocales2(): void
    {
        $property = (new Property())->setPostcode('');
        $tenancyReview = (new TenancyReview())->setProperty($property);

        $locales = $this->tenancyReviewService->generateLocales($tenancyReview);

        $this->assertEmpty($locales);
    }

    /**
     * @covers \App\Service\TenancyReviewService::submitReview
     * Test successful review submission.
     */
    public function testSubmitReview1(): void
    {
        $requestDetails = $this->prophesize(RequestDetails::class);

        list($input, $user) = $this->prophesizeSubmitReview();

        $this->interactionService->record('Review', 45, $requestDetails, $user)
            ->shouldBeCalledOnce();

        $submitOutput = $this->tenancyReviewService->submitReview(
            $input->reveal(),
            $user->reveal(),
            $requestDetails->reveal()
        );

        $this->assertEquals(true, $submitOutput->isSuccess());
    }

    /**
     * @covers \App\Service\TenancyReviewService::submitReview
     * Test catching of exception when thrown by InteractionService::record.
     */
    public function testSubmitReview2(): void
    {
        $requestDetails = $this->prophesize(RequestDetails::class);

        list($input, $user) = $this->prophesizeSubmitReview();

        $this->interactionService->record('Review', 45, $requestDetails, $user)
            ->shouldBeCalledOnce()
            ->willThrow(UnexpectedValueException::class);

        $submitOutput = $this->tenancyReviewService->submitReview(
            $input->reveal(),
            $user->reveal(),
            $requestDetails->reveal()
        );

        $this->assertEquals(true, $submitOutput->isSuccess());
    }

    private function prophesizeSubmitReview(): array
    {
        $input = $this->prophesize(SubmitInput::class);
        $user = $this->prophesize(User::class);
        $property = $this->prophesize(Property::class);
        $agency = $this->prophesize(Agency::class);
        $branch = $this->prophesize(Branch::class);
        $tenancyReview = $this->prophesize(TenancyReview::class);

        $input->getPropertySlug()->shouldBeCalledOnce()->willReturn('propertyslug');
        $input->getAgencyName()->shouldBeCalledOnce()->willReturn('Test Agency Name');
        $input->getAgencyBranch()->shouldBeCalledOnce()->willReturn('Testerton');
        $input->getCode()->shouldBeCalledOnce()->willReturn('testcode');

        $this->propertyRepository->findOnePublishedBySlug('propertyslug')->shouldBeCalledOnce()->willReturn($property);

        $this->agencyFindOrCreateService->findOrCreateByName('Test Agency Name')->shouldBeCalledOnce()->willReturn($agency);

        $this->branchFindOrCreateService->findOrCreate('Testerton', $agency)->shouldBeCalledOnce()->willReturn($branch);

        $this->userService->getUserEntityOrNullFromUserInterface($user)->shouldBeCalledOnce()->willReturn($user);

        $this->tenancyReviewFactory->createEntity($input, $property, $branch, $user)
            ->shouldBeCalledOnce()
            ->willReturn($tenancyReview)
        ;

        $this->completeService->complete('testcode', Argument::type(TenancyReview::class))->shouldBeCalledOnce();

        $this->assertEntitiesArePersistedAndFlush([$tenancyReview]);

        $this->notificationService->sendTenancyReviewModerationNotification(Argument::type(TenancyReview::class))->shouldBeCalledOnce();

        $tenancyReview->getId()
            ->shouldBeCalledOnce()
            ->willReturn(45);

        return [$input, $user];
    }
}
