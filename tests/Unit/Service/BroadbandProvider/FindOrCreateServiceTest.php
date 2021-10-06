<?php

namespace App\Tests\Unit\Service\BroadbandProvider;

use App\Entity\BroadbandProvider;
use App\Factory\BroadbandProviderFactory;
use App\Repository\BroadbandProviderRepository;
use App\Service\BroadbandProvider\FindOrCreateService;
use App\Tests\Unit\EntityManagerTrait;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

final class FindOrCreateServiceTest extends TestCase
{
    use ProphecyTrait;
    use EntityManagerTrait;

    private const COUNTRY_CODE = 'UK';

    private FindOrCreateService $findOrCreateService;

    private ObjectProphecy $broadbandProviderFactory;
    private ObjectProphecy $broadbandProviderRepository;

    public function setUp(): void
    {
        $this->entityManager = $this->prophesize(EntityManagerInterface::class);
        $this->broadbandProviderFactory = $this->prophesize(BroadbandProviderFactory::class);
        $this->broadbandProviderRepository = $this->prophesize(BroadbandProviderRepository::class);

        $this->findOrCreateService = new FindOrCreateService(
            $this->entityManager->reveal(),
            $this->broadbandProviderFactory->reveal(),
            $this->broadbandProviderRepository->reveal(),
        );
    }

    public function testFindOrCreateWhenNotExists(): void
    {
        $broadbandProvider = $this->prophesize(BroadbandProvider::class);
        $name = 'Superspeed Home';

        $this->broadbandProviderRepository->findOneBy(
            [
            'name' => $name,
            'countryCode' => self::COUNTRY_CODE,
            ]
        )
            ->shouldBeCalledOnce()
            ->willReturn(null);

        $this->broadbandProviderFactory->createEntityFromNameAndCountryCode($name, self::COUNTRY_CODE)
            ->shouldBeCalledOnce()
            ->willReturn($broadbandProvider);

        $result = $this->findOrCreateService->findOrCreate($name, self::COUNTRY_CODE);

        $this->assertEntitiesArePersistedAndFlush([$broadbandProvider]);
        $this->assertEquals($broadbandProvider->reveal(), $result);
    }

    public function testFindOrCreateWhenAlreadyExists(): void
    {
        $name = 'Business Internet Co';

        $broadbandProvider = $this->prophesize(BroadbandProvider::class);

        $this->broadbandProviderRepository->findOneBy(
            [
                'name' => $name,
                'countryCode' => self::COUNTRY_CODE,
            ]
        )
            ->shouldBeCalledOnce()
            ->willReturn($broadbandProvider);

        $this->assertEntityManagerUnused();

        $result = $this->findOrCreateService->findOrCreate($name, self::COUNTRY_CODE);

        $this->assertEntityManagerUnused();
        $this->assertEquals($broadbandProvider->reveal(), $result);
    }
}
