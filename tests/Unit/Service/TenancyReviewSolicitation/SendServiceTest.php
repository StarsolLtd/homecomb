<?php

namespace App\Tests\Unit\Service\TenancyReviewSolicitation;

use App\Entity\Agency;
use App\Entity\Branch;
use App\Entity\Property;
use App\Entity\TenancyReviewSolicitation;
use App\Entity\User;
use App\Exception\DeveloperException;
use App\Service\EmailService;
use App\Service\TenancyReviewSolicitation\SendService;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class SendServiceTest extends TestCase
{
    use ProphecyTrait;

    private SendService $sendService;

    private ObjectProphecy $emailService;

    public function setUp(): void
    {
        $requestStack = $this->prophesize(RequestStack::class);
        $this->emailService = $this->prophesize(EmailService::class);

        $request = $this->prophesize(Request::class);
        $requestStack->getCurrentRequest()->willReturn($request);
        $request->getSchemeAndHttpHost()->willReturn('https://homecomb.co.uk/');

        $this->sendService = new SendService(
            $requestStack->reveal(),
            $this->emailService->reveal(),
        );
    }

    public function testSend1(): void
    {
        $user = $this->prophesize(User::class);
        $agency = $this->prophesize(Agency::class);
        $branch = $this->prophesize(Branch::class);
        $property = $this->prophesize(Property::class);

        $tenancyReviewSolicitation = $this->prophesize(TenancyReviewSolicitation::class);

        $branch->getAgency()
            ->shouldBeCalledOnce()
            ->willReturn($agency);

        $agency->getName()
            ->shouldBeCalledOnce()
            ->willReturn('Dereham Residential');

        $tenancyReviewSolicitation->getCode()
            ->shouldBeCalledOnce()
            ->willReturn('testcode');

        $tenancyReviewSolicitation->getRecipientFirstName()
            ->shouldBeCalledOnce()
            ->willReturn('Jack');

        $tenancyReviewSolicitation->getRecipientLastName()
            ->shouldBeCalledOnce()
            ->willReturn('Parnell');

        $tenancyReviewSolicitation->getBranch()
            ->shouldBeCalledOnce()
            ->willReturn($branch);

        $tenancyReviewSolicitation->getProperty()
            ->shouldBeCalledOnce()
            ->willReturn($property);

        $tenancyReviewSolicitation->getRecipientEmail()
            ->shouldBeCalledOnce()
            ->willReturn('sample.tenant@starsol.co.uk');

        $property->getAddressLine1()
            ->shouldBeCalledOnce()
            ->willReturn('15 Salmon Street');

        $this->emailService->process(
            'sample.tenant@starsol.co.uk',
            'Jack Parnell',
            'Please review your tenancy at 15 Salmon Street with Dereham Residential',
            'review-solicitation',
            Argument::type('array'),
            null,
            $user->reveal()
        )->shouldBeCalledOnce();

        $this->sendService->send($tenancyReviewSolicitation->reveal(), $user->reveal());
    }

    /**
     * Test throws DeveloperException when Branch has no Agency.
     */
    public function testSend2(): void
    {
        $user = $this->prophesize(User::class);
        $branch = $this->prophesize(Branch::class);
        $tenancyReviewSolicitation = $this->prophesize(TenancyReviewSolicitation::class);

        $tenancyReviewSolicitation->getCode()->shouldBeCalledOnce()->willReturn('test-code');
        $tenancyReviewSolicitation->getBranch()->shouldBeCalledOnce()->willReturn($branch);
        $branch->getAgency()->shouldBeCalledOnce()->willReturn(null);

        $this->expectException(DeveloperException::class);

        $this->sendService->send($tenancyReviewSolicitation->reveal(), $user->reveal());
    }
}
