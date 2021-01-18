<?php

namespace App\Tests\Unit\Service;

use App\Entity\Flag\Flag;
use App\Entity\Interaction\Interaction;
use App\Entity\Review;
use App\Entity\User;
use App\Exception\UnexpectedValueException;
use App\Repository\FlagRepository;
use App\Repository\ReviewRepository;
use App\Service\InteractionService;
use App\Service\UserService;
use App\Tests\Unit\EntityManagerTrait;
use App\Tests\Unit\UserEntityFromInterfaceTrait;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @covers \App\Service\InteractionService
 */
class InteractionServiceTest extends TestCase
{
    use EntityManagerTrait;
    use ProphecyTrait;
    use UserEntityFromInterfaceTrait;

    private InteractionService $interactionService;

    private $flagRepository;
    private $reviewRepository;

    public function setUp(): void
    {
        $this->entityManager = $this->prophesize(EntityManagerInterface::class);
        $this->userService = $this->prophesize(UserService::class);
        $this->flagRepository = $this->prophesize(FlagRepository::class);
        $this->reviewRepository = $this->prophesize(ReviewRepository::class);

        $this->interactionService = new InteractionService(
            $this->entityManager->reveal(),
            $this->userService->reveal(),
            $this->flagRepository->reveal(),
            $this->reviewRepository->reveal(),
        );
    }

    /**
     * @covers \App\Service\InteractionService::record
     * Test successfully record a ReviewInteraction
     */
    public function testRecord1(): void
    {
        $user = new User();
        $review = $this->prophesize(Review::class);

        $this->reviewRepository->findOneById(789)
            ->shouldBeCalledOnce()
            ->willReturn($review)
        ;

        $this->assertGetUserEntityOrNullFromInterface($user);

        $this->entityManager->persist(Argument::type(Interaction::class))->shouldBeCalledOnce();
        $this->entityManager->flush()->shouldBeCalledOnce();

        $this->interactionService->record(
            'Review',
            789,
            $user,
            '123456789',
            '1.2.3.4',
            'Godzilla 42.0'
        );
    }

    /**
     * @covers \App\Service\InteractionService::record
     * Test successfully record a FlagInteraction
     */
    public function testRecord2(): void
    {
        $user = new User();
        $flag = $this->prophesize(Flag::class);

        $this->flagRepository->findOneById(2020)
            ->shouldBeCalledOnce()
            ->willReturn($flag)
        ;

        $this->assertGetUserEntityOrNullFromInterface($user);

        $this->entityManager->persist(Argument::type(Interaction::class))->shouldBeCalledOnce();
        $this->entityManager->flush()->shouldBeCalledOnce();

        $this->interactionService->record(
            'Flag',
            2020,
            $user,
            '123456789',
            '1.2.3.4',
            'Godzilla 42.0'
        );
    }

    /**
     * @covers \App\Service\InteractionService::record
     * Test throws exception if entity not supported
     */
    public function testRecord3(): void
    {
        $this->assertEntityManagerUnused();

        $this->expectException(UnexpectedValueException::class);

        $this->interactionService->record(
            'Sushi',
            20
        );
    }
}
