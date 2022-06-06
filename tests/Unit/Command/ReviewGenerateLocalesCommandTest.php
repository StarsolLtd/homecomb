<?php

namespace App\Tests\Unit\Command;

use App\Command\ReviewGenerateLocalesCommand;
use App\Entity\Locale\Locale;
use App\Entity\TenancyReview;
use App\Exception\NotFoundException;
use App\Repository\TenancyReviewRepositoryInterface;
use App\Service\TenancyReview\GenerateLocalesService;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use RuntimeException;
use Symfony\Component\Console\Command\Command;

final class ReviewGenerateLocalesCommandTest extends TestCase
{
    use CommandTestTrait;
    use ProphecyTrait;

    private int $reviewId = 234;

    private ReviewGenerateLocalesCommand $command;

    private ObjectProphecy $tenancyReviewRepository;
    private ObjectProphecy $generateLocalesService;

    public function setUp(): void
    {
        $this->tenancyReviewRepository = $this->prophesize(TenancyReviewRepositoryInterface::class);
        $this->generateLocalesService = $this->prophesize(GenerateLocalesService::class);

        $this->command = new ReviewGenerateLocalesCommand(
            $this->tenancyReviewRepository->reveal(),
            $this->generateLocalesService->reveal(),
        );

        $this->setupCommandTester('review:generate-locales');
    }

    /**
     * Test happy path where tenancy review exists and locales are generated.
     */
    public function testExecute1(): void
    {
        $tenancyReview = $this->prophesize(TenancyReview::class);
        $this->tenancyReviewRepository->find($this->reviewId)->shouldBeCalledOnce()->willReturn($tenancyReview);

        // There were some problems prophesizing these, so using real objects
        $locale1 = (new Locale())->setName('Islington');
        $locale2 = (new Locale())->setName('Clerkenwell');
        $locales = new ArrayCollection();
        $locales->add($locale1);
        $locales->add($locale2);

        $this->generateLocalesService->generateLocales($tenancyReview)->shouldBeCalledOnce()->willReturn($locales);

        $result = $this->commandTester->execute(['arg1' => (string) $this->reviewId]);

        $this->assertEquals(Command::SUCCESS, $result);

        $display = $this->commandTester->getDisplay();

        $this->assertStringContainsString('Generating locals for review '.$this->reviewId, $display);
        $this->assertStringContainsString('Locale Islington associated with review', $display);
        $this->assertStringContainsString('Locale Clerkenwell associated with review', $display);
    }

    /**
     * Test an exception is thrown when tenancy review not found.
     */
    public function testExecute2(): void
    {
        $this->tenancyReviewRepository->find($this->reviewId)->shouldBeCalledOnce()->willReturn(null);

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('TenancyReview '.$this->reviewId.' not found.');

        $this->commandTester->execute(['arg1' => $this->reviewId]);
    }

    /**
     * Test an exception is thrown when argument type is invalid.
     */
    public function testExecute3(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid type of arg1: boolean');

        $this->commandTester->execute(['arg1' => true]);
    }
}
