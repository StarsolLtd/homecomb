<?php

namespace App\Tests\Unit\Service;

use App\Entity\Comment\Comment;
use App\Entity\User;
use App\Exception\UnexpectedValueException;
use App\Factory\CommentFactory;
use App\Model\Comment\SubmitInput;
use App\Service\CommentService;
use App\Service\UserService;
use App\Tests\Unit\EntityManagerTrait;
use App\Tests\Unit\UserEntityFromInterfaceTrait;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Log\LoggerInterface;

/**
 * @covers \App\Service\CommentService
 */
class CommentServiceTest extends TestCase
{
    use EntityManagerTrait;
    use ProphecyTrait;
    use UserEntityFromInterfaceTrait;

    private CommentService $commentService;

    private $logger;
    private $commentFactory;

    public function setUp(): void
    {
        $this->entityManager = $this->prophesize(EntityManagerInterface::class);
        $this->logger = $this->prophesize(LoggerInterface::class);
        $this->commentFactory = $this->prophesize(CommentFactory::class);
        $this->userService = $this->prophesize(UserService::class);

        $this->commentService = new CommentService(
            $this->entityManager->reveal(),
            $this->logger->reveal(),
            $this->commentFactory->reveal(),
            $this->userService->reveal(),
        );
    }

    /**
     * @covers \App\Service\CommentService::submitComment
     */
    public function testSubmitComment1(): void
    {
        $submitInput = $this->prophesize(SubmitInput::class);
        $comment = $this->prophesize(Comment::class);
        $user = new User();

        $this->assertGetUserEntityFromInterface($user);

        $this->commentFactory->createEntityFromSubmitInput($submitInput, $user)
            ->shouldBeCalledOnce()
            ->willReturn($comment);

        $this->assertEntitiesArePersistedAndFlush([$comment]);

        $output = $this->commentService->submitComment($submitInput->reveal(), $user);

        $this->assertTrue($output->isSuccess());
    }

    /**
     * @covers \App\Service\CommentService::submitComment
     * Test log and rethrow UnexpectedValueException
     */
    public function testSubmitComment2(): void
    {
        $submitInput = $this->prophesize(SubmitInput::class);
        $user = new User();

        $this->assertGetUserEntityFromInterface($user);

        $this->commentFactory->createEntityFromSubmitInput($submitInput, $user)
            ->shouldBeCalledOnce()
            ->willThrow(UnexpectedValueException::class);

        $this->logger->warning(Argument::type('string'))
            ->shouldBeCalledOnce();

        $this->expectException(UnexpectedValueException::class);

        $this->assertEntityManagerUnused();

        $this->commentService->submitComment($submitInput->reveal(), $user);
    }
}
