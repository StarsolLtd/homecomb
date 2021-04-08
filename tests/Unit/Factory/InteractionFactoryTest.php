<?php

namespace App\Tests\Unit\Factory;

use App\Factory\InteractionFactory;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @covers \App\Factory\InteractionFactory
 */
class InteractionFactoryTest extends TestCase
{
    use ProphecyTrait;

    private InteractionFactory $interactionFactory;

    public function setUp(): void
    {
        $this->interactionFactory = new InteractionFactory();
    }

    /**
     * @covers \App\Factory\InteractionFactory::getRequestDetails
     */
    public function testGetRequestDetails1(): void
    {
        $session = $this->prophesize(Session::class);

        // Can't work out how to prophesize $request->headers->get('User-Agent'), so creating actual object
        $request = Request::create(
            '',
            '',
            [],
            [],
            [],
            [
                'REMOTE_ADDR' => '1.2.3.4',
                'HTTP_USER_AGENT' => 'Godzilla 42.0',
            ],
        );
        $request->setSession($session->reveal());

        $session
            ->getId()
            ->shouldBeCalledOnce()
            ->willReturn('1234567890');

        $model = $this->interactionFactory->getRequestDetails($request);

        $this->assertEquals('1234567890', $model->getSessionId());
        $this->assertEquals('1.2.3.4', $model->getIpAddress());
        $this->assertEquals('Godzilla 42.0', $model->getUserAgent());
    }
}
