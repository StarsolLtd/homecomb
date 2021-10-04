<?php

namespace App\Tests\Unit\Service\TenancyReview;

use App\Entity\Locale\Locale;
use App\Entity\Postcode;
use App\Entity\Property;
use App\Entity\TenancyReview;
use App\Repository\PostcodeRepository;
use App\Service\TenancyReview\GenerateLocalesService;
use App\Tests\Unit\EntityManagerTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

final class GenerateLocalesServiceTest extends TestCase
{
    use ProphecyTrait;
    use EntityManagerTrait;

    private GenerateLocalesService $tenancyReviewService;

    private ObjectProphecy $postcodeRepository;

    public function setUp(): void
    {
        $this->entityManager = $this->prophesize(EntityManagerInterface::class);
        $this->postcodeRepository = $this->prophesize(PostcodeRepository::class);

        $this->tenancyReviewService = new GenerateLocalesService(
            $this->entityManager->reveal(),
            $this->postcodeRepository->reveal(),
        );
    }

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

    public function testGenerateLocales2(): void
    {
        $property = (new Property())->setPostcode('');
        $tenancyReview = (new TenancyReview())->setProperty($property);

        $locales = $this->tenancyReviewService->generateLocales($tenancyReview);

        $this->assertEmpty($locales);
    }
}
