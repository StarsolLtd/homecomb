<?php

namespace App\Tests\Unit\Service;

use App\Service\SessionService;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @covers \App\Service\SessionService
 */
class SessionServiceTest extends TestCase
{
    use ProphecyTrait;

    private SessionService $sessionService;

    private ObjectProphecy $session;

    public function setUp(): void
    {
        $this->session = $this->prophesize(SessionInterface::class);

        $this->sessionService = new SessionService($this->session->reveal());
    }

    /**
     * @covers \App\Service\SessionService::get
     */
    public function testGet1(): void
    {
        $this->session->get('test')
            ->shouldBeCalledOnce()
            ->willReturn('val');

        $output = $this->sessionService->get('test');

        $this->assertEquals('val', $output);
    }

    /**
     * @covers \App\Service\SessionService::set
     */
    public function testSet1(): void
    {
        $this->session->set('test', 'oval')->shouldBeCalledOnce();

        $this->sessionService->set('test', 'oval');
    }
}
