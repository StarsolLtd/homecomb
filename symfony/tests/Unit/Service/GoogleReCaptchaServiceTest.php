<?php

namespace App\Tests\Unit\Service;

use App\Service\GoogleReCaptchaService;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use ReCaptcha\ReCaptcha;
use ReCaptcha\Response;

/**
 * @covers \App\Service\GoogleReCaptchaService
 */
class GoogleReCaptchaServiceTest extends TestCase
{
    use ProphecyTrait;

    private const CLIENT_IP = '1.1.1.1';
    private const EXPECTED_HOST_NAME = 'hostname.com';
    private const TOKEN = 'sampletoken';

    private GoogleReCaptchaService $googleReCaptchaService;

    private $reCaptcha;

    public function setUp(): void
    {
        $this->reCaptcha = $this->prophesize(ReCaptcha::class);

        $this->googleReCaptchaService = new GoogleReCaptchaService(
            true,
            $this->reCaptcha->reveal()
        );
    }

    /**
     * @covers \App\Service\GoogleReCaptchaService::verify
     * Test verify returns true when response isSuccess value is true.
     */
    public function testVerify1(): void
    {
        $resp = $this->prophesize(Response::class);

        $this->prophesizeVerify($resp);

        $resp->isSuccess()->shouldBeCalledOnce()->willReturn(true);

        $output = $this->googleReCaptchaService->verify(
            self::TOKEN,
            self::CLIENT_IP,
            self::EXPECTED_HOST_NAME
        );

        $this->assertTrue($output);
    }

    /**
     * @covers \App\Service\GoogleReCaptchaService::verify
     * Test verify returns false when response isSuccess value is false.
     */
    public function testVerify2(): void
    {
        $resp = $this->prophesize(Response::class);

        $this->prophesizeVerify($resp);

        $resp->isSuccess()->shouldBeCalledOnce()->willReturn(false);

        $output = $this->googleReCaptchaService->verify(
            self::TOKEN,
            self::CLIENT_IP,
            self::EXPECTED_HOST_NAME
        );

        $this->assertFalse($output);
    }

    /**
     * @covers \App\Service\GoogleReCaptchaService::verify
     * Test verify returns false when token is null.
     */
    public function testVerify3(): void
    {
        $this->reCaptcha->verify(Argument::any(), Argument::any())->shouldNotBeCalled();

        $output = $this->googleReCaptchaService->verify(
            null,
            self::CLIENT_IP,
            self::EXPECTED_HOST_NAME
        );

        $this->assertFalse($output);
    }

    /**
     * @covers \App\Service\GoogleReCaptchaService::verify
     * Test verify returns trues when check not needed. This test doesn't use service from setUp.
     */
    public function testVerify4(): void
    {
        $googleReCaptchaService = new GoogleReCaptchaService(
            false,
            $this->reCaptcha->reveal()
        );

        $this->reCaptcha->verify(Argument::any(), Argument::any())->shouldNotBeCalled();

        $output = $googleReCaptchaService->verify(
            'whatever',
            '5.6.7.8',
            'does.not.matter'
        );

        $this->assertTrue($output);
    }

    private function prophesizeVerify(ObjectProphecy $resp): void
    {
        $this->reCaptcha->setExpectedHostname(self::EXPECTED_HOST_NAME)
            ->shouldBeCalledOnce()
            ->willReturn($this->reCaptcha);

        $this->reCaptcha->setScoreThreshold(GoogleReCaptchaService::THRESHOLD)
            ->shouldBeCalledOnce()
            ->willReturn($this->reCaptcha);

        $this->reCaptcha->verify(self::TOKEN, self::CLIENT_IP)
            ->shouldBeCalledOnce()
            ->willReturn($resp);
    }
}
