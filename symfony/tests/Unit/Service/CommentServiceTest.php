<?php

namespace App\Tests\Unit\Service;

use App\Entity\Comment\Comment;
use App\Entity\User;
use App\Factory\CommentFactory;
use App\Model\Comment\SubmitInput;
use App\Service\CommentService;
use App\Service\UserService;
use App\Tests\Unit\EntityManagerTrait;
use App\Tests\Unit\UserEntityFromInterfaceTrait;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @covers \App\Service\CommentService
 */
class CommentServiceTest extends TestCase
{
    use EntityManagerTrait;
    use ProphecyTrait;
    use UserEntityFromInterfaceTrait;

    private CommentService $commentService;

    private $commentFactory;

    public function setUp(): void
    {
        $this->entityManager = $this->prophesize(EntityManagerInterface::class);
        $this->commentFactory = $this->prophesize(CommentFactory::class);
        $this->userService = $this->prophesize(UserService::class);

        $this->commentService = new CommentService(
            $this->entityManager->reveal(),
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
}
