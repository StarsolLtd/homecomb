<?php

namespace App\Tests\Unit\Factory;

use App\Entity\BroadbandProvider;
use App\Entity\BroadbandProviderReview;
use App\Entity\User;
use App\Factory\BroadbandProviderReviewFactory;
use App\Model\BroadbandProviderReview\SubmitInput;
use App\Util\BroadbandProviderReviewHelper;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

final class BroadbandProviderReviewFactoryTest extends TestCase
{
    use ProphecyTrait;

    private BroadbandProviderReviewFactory $broadbandProviderReviewFactory;

    private ObjectProphecy $broadbandProviderReviewHelper;

    public function setUp(): void
    {
        $this->broadbandProviderReviewHelper = $this->prophesize(BroadbandProviderReviewHelper::class);

        $this->broadbandProviderReviewFactory = new BroadbandProviderReviewFactory(
            $this->broadbandProviderReviewHelper->reveal(),
        );
    }

    public function testCreateEntity1(): void
    {
        $broadbandProvider = $this->prophesize(BroadbandProvider::class);
        $user = $this->prophesize(User::class);
        $input = $this->prophesize(SubmitInput::class);

        $input->getReviewerName()
            ->shouldBeCalledOnce()
            ->willReturn('Jo Smith');

        $input->getReviewTitle()
            ->shouldBeCalledOnce()
            ->willReturn('Slow like a snail');

        $input->getReviewContent()
            ->shouldBeCalledOnce()
            ->willReturn('It took me a day to download a GIF');

        $input->getOverallStars()
            ->shouldBeCalledOnce()
            ->willReturn(2);

        $this->broadbandProviderReviewHelper->generateSlug(Argument::type(BroadbandProviderReview::class))
            ->shouldBeCalledOnce()
            ->willReturn('test-bpr-slug');

        $entity = $this->broadbandProviderReviewFactory->createEntity(
            $input->reveal(),
            $broadbandProvider->reveal(),
            $user->reveal()
        );

        $this->assertEquals('Jo Smith', $entity->getAuthor());
        $this->assertEquals('Slow like a snail', $entity->getTitle());
        $this->assertEquals('It took me a day to download a GIF', $entity->getContent());
        $this->assertEquals(2, $entity->getOverallStars());
        $this->assertEquals('test-bpr-slug', $entity->getSlug());
    }
}
