<?php

namespace App\Tests\Unit\Service;

use App\Entity\Flag\Flag;
use App\Entity\Interaction\Interaction;
use App\Entity\Review;
use App\Entity\Survey\Answer;
use App\Entity\User;
use App\Exception\UnexpectedValueException;
use App\Model\Interaction\RequestDetails;
use App\Repository\FlagRepository;
use App\Repository\ReviewRepository;
use App\Repository\Survey\AnswerRepository;
use App\Service\InteractionService;
use App\Service\UserService;
use App\Tests\Unit\EntityManagerTrait;
use App\Tests\Unit\UserEntityFromInterfaceTrait;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @covers \App\Service\InteractionService
 */
class InteractionServiceTest extends TestCase
{
    use EntityManagerTrait;
    use ProphecyTrait;
    use UserEntityFromInterfaceTrait;

    private InteractionService $interactionService;

    private $answerRepository;
    private $flagRepository;
    private $reviewRepository;

    public function setUp(): void
    {
        $this->entityManager = $this->prophesize(EntityManagerInterface::class);
        $this->userService = $this->prophesize(UserService::class);
        $this->answerRepository = $this->prophesize(AnswerRepository::class);
        $this->flagRepository = $this->prophesize(FlagRepository::class);
        $this->reviewRepository = $this->prophesize(ReviewRepository::class);

        $this->interactionService = new InteractionService(
            $this->entityManager->reveal(),
            $this->userService->reveal(),
            $this->answerRepository->reveal(),
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
        $requestDetails = $this->prophesize(RequestDetails::class);
        $this->prophesizeRequestDetails($requestDetails);

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
            $requestDetails->reveal(),
            $user
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
        $requestDetails = $this->prophesize(RequestDetails::class);
        $this->prophesizeRequestDetails($requestDetails);

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
            $requestDetails->reveal(),
            $user
        );
    }

    /**
     * @covers \App\Service\InteractionService::record
     * Test successfully record an AnswerInteraction
     */
    public function testRecord3(): void
    {
        $user = new User();
        $answer = $this->prophesize(Answer::class);
        $requestDetails = $this->prophesize(RequestDetails::class);
        $this->prophesizeRequestDetails($requestDetails);

        $this->answerRepository->findOneById(2020)
            ->shouldBeCalledOnce()
            ->willReturn($answer);

        $this->assertGetUserEntityOrNullFromInterface($user);

        $this->entityManager->persist(Argument::type(Interaction::class))->shouldBeCalledOnce();
        $this->entityManager->flush()->shouldBeCalledOnce();

        $this->interactionService->record(
            'Answer',
            2020,
            $requestDetails->reveal(),
            $user
        );
    }

    /**
     * @covers \App\Service\InteractionService::record
     * Test throws exception if entity not supported
     */
    public function testRecord4(): void
    {
        $requestDetails = $this->prophesize(RequestDetails::class);

        $this->assertEntityManagerUnused();

        $this->expectException(UnexpectedValueException::class);

        $this->interactionService->record(
            'Sushi',
            20,
            $requestDetails->reveal()
        );
    }

    private function prophesizeRequestDetails(ObjectProphecy $requestDetails): void
    {
        $requestDetails
            ->getSessionId()
            ->shouldBeCalledOnce()
            ->willReturn('123456789')
        ;

        $requestDetails
            ->getIpAddress()
            ->shouldBeCalledOnce()
            ->willReturn('1.2.3.4')
        ;

        $requestDetails
            ->getUserAgent()
            ->shouldBeCalledOnce()
            ->willReturn('Godzilla 42.0')
        ;
    }
}