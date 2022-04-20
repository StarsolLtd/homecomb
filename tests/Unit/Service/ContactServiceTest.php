<?php

namespace App\Tests\Unit\Service;

use App\Model\Contact\SubmitInputInterface;
use App\Service\ContactService;
use App\Service\EmailService;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @covers \App\Service\ContactService
 */
final class ContactServiceTest extends TestCase
{
    use ProphecyTrait;

    private ContactService $contactService;

    private ObjectProphecy $emailService;

    public function setUp(): void
    {
        $this->emailService = $this->prophesize(EmailService::class);

        $this->contactService = new ContactService(
            $this->emailService->reveal(),
            'HomeComb',
            'jack@starsol.co.uk',
        );
    }

    /**
     * @covers \App\Service\ContactService::submitContact
     */
    public function testSubmitContact1(): void
    {
        $input = $this->prophesize(SubmitInputInterface::class);
        $input->getEmailAddress()->shouldBeCalledOnce()->willReturn('fiona.dutton@starsol.co.uk');
        $input->getName()->shouldBeCalledOnce()->willReturn('Fiona Dutton');
        $input->getMessage()->shouldBeCalledOnce()->willReturn('This is a test.');

        $this->emailService->process(
            'jack@starsol.co.uk',
            'HomeComb',
            'HomeComb Contact Form Submission',
            'contact',
            [
                'fromEmail' => 'fiona.dutton@starsol.co.uk',
                'fromName' => 'Fiona Dutton',
                'message' => 'This is a test.',
            ]
        )->shouldBeCalledOnce();

        $output = $this->contactService->submitContact($input->reveal());

        $this->assertTrue($output->isSuccess());
    }
}
