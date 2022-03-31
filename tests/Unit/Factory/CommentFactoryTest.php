<?php

namespace App\Tests\Unit\Factory;

use App\Entity\Comment\TenancyReviewComment;
use App\Entity\TenancyReview;
use App\Entity\User;
use App\Exception\UnexpectedValueException;
use App\Factory\CommentFactory;
use App\Model\Comment\SubmitInputInterface;
use App\Repository\TenancyReviewRepository;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @covers \App\Factory\CommentFactory
 */
final class CommentFactoryTest extends TestCase
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
        $input = $this->prophesize(SubmitInputInterface::class);
        $input->getEntityName()->shouldBeCalledOnce()->willReturn('Review');
        $input->getEntityId()->shouldBeCalledOnce()->willReturn(876);
        $input->getContent()->shouldBeCalledOnce()->willReturn('On behalf of my Agency I would like to say thank you for your review.');

        $user = new User();
        $tenancyReview = new TenancyReview();

        $this->tenancyReviewRepository->findOnePublishedById(876)
            ->shouldBeCalledOnce()
            ->willReturn($tenancyReview);

        $comment = $this->commentFactory->createEntityFromSubmitInput($input->reveal(), $user);

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
        $input = $this->prophesize(SubmitInputInterface::class);
        $input->getEntityName()->shouldBeCalledOnce()->willReturn('Fondue');
        $input->getEntityId()->shouldBeCalledOnce()->willReturn(876);
        $user = new User();

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Fondue is not a valid comment related entity name.');

        $this->commentFactory->createEntityFromSubmitInput($input->reveal(), $user);
    }
}
