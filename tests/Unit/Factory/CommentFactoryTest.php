<?php

namespace App\Tests\Unit\Factory;

use App\Entity\Comment\TenancyReviewComment;
use App\Entity\TenancyReview;
use App\Entity\User;
use App\Exception\UnexpectedValueException;
use App\Factory\CommentFactory;
use App\Model\Comment\SubmitInput;
use App\Repository\TenancyReviewRepository;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @covers \App\Factory\CommentFactory
 */
class CommentFactoryTest extends TestCase
{
    use ProphecyTrait;

    private CommentFactory $commentFactory;

    private ObjectProphecy $tenancyReviewRepository;

    public function setUp(): void
    {
        $this->tenancyReviewRepository = $this->prophesize(TenancyReviewRepository::class);

        $this->commentFactory = new CommentFactory(
            $this->tenancyReviewRepository->reveal(),
        );
    }

    /**
     * @covers \App\Factory\CommentFactory::createEntityFromSubmitInput
     */
    public function testCreateEntityFromSubmitInput1(): void
    {
        $input = new SubmitInput(
            'Review',
            876,
            'On behalf of my Agency I would like to say thank you for your review.',
            null
        );
        $user = new User();
        $tenancyReview = new TenancyReview();

        $this->tenancyReviewRepository->findOnePublishedById($input->getEntityId())
            ->shouldBeCalledOnce()
            ->willReturn($tenancyReview);

        $comment = $this->commentFactory->createEntityFromSubmitInput($input, $user);

        $this->assertInstanceOf(TenancyReviewComment::class, $comment);
        $this->assertEquals(876, $comment->getRelatedEntityId());
        $this->assertEquals('On behalf of my Agency I would like to say thank you for your review.', $comment->getContent());
        $this->assertEquals($user, $comment->getUser());
    }

    /**
     * @covers \App\Factory\CommentFactory::createEntityFromSubmitInput
     * Test throws exception when entityName is not supported.
     */
    public function testCreateEntityFromSubmitInput2(): void
    {
        $input = new SubmitInput(
            'Fondue',
            876,
            'On behalf of my Agency I would like to say thank you for your review.',
            null
        );
        $user = new User();

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Fondue is not a valid comment related entity name.');

        $this->commentFactory->createEntityFromSubmitInput($input, $user);
    }
}
