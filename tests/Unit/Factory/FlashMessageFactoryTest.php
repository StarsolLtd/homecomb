<?php

namespace App\Tests\Unit\Factory;

use App\Factory\FlashMessageFactory;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;

final class FlashMessageFactoryTest extends TestCase
{
    use ProphecyTrait;

    private FlashMessageFactory $flashMessageFactory;

    public function setUp(): void
    {
        $this->flashMessageFactory = new FlashMessageFactory();
    }

    public function testGetFlashMessages(): void
    {
        $flashBag = $this->prophesize(FlashBag::class);

        $flashBag->keys()
            ->shouldBeCalledOnce()
            ->willReturn(['notice', 'warning', 'error']);

        $flashBag->get('notice')
            ->shouldBeCalledOnce()
            ->willReturn(['Your alligator keeper is not paid enough.', 'Your alligator keeper quit her job.']);

        $flashBag->get('warning')
            ->shouldBeCalledOnce()
            ->willReturn(['Your alligators are hungry.']);

        $flashBag->get('error')
            ->shouldBeCalledOnce()
            ->willReturn(['You have been eaten by your alligators.']);

        $view = $this->flashMessageFactory->getFlashMessages($flashBag->reveal());

        $this->assertCount(4, $view->getMessages());

        $this->assertEquals('notice', $view->getMessages()[0]->getType());
        $this->assertEquals('notice', $view->getMessages()[1]->getType());
        $this->assertEquals('warning', $view->getMessages()[2]->getType());
        $this->assertEquals('error', $view->getMessages()[3]->getType());

        $this->assertEquals('Your alligator keeper is not paid enough.', $view->getMessages()[0]->getMessage());
        $this->assertEquals('Your alligator keeper quit her job.', $view->getMessages()[1]->getMessage());
        $this->assertEquals('Your alligators are hungry.', $view->getMessages()[2]->getMessage());
        $this->assertEquals('You have been eaten by your alligators.', $view->getMessages()[3]->getMessage());
    }
}
