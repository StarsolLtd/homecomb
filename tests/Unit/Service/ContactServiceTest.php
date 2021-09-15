<?php

namespace App\Tests\Unit\Service;

use App\Model\Contact\SubmitInput;
use App\Service\ContactService;
use App\Service\EmailService;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @covers \App\Service\ContactService
 */
class ContactServiceTest extends TestCase
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
            'jack@starsol.co.uk'
        );
    }

    /**
     * @covers \App\Service\ContactService::submitContact
     */
    public function testSubmitContact1(): void
    {
        $input = new SubmitInput(
            'fiona.dutton@starsol.co.uk',
            'Fiona Dutton',
            'This is a test.'
        );

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

        $output = $this->contactService->submitContact($input);

        $this->assertTrue($output->isSuccess());
    }
}
