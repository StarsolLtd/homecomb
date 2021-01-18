<?php

namespace App\Tests\Unit\Factory;

use App\Entity\Agency;
use App\Entity\Branch;
use App\Entity\Comment\ReviewComment;
use App\Entity\Property;
use App\Entity\Review;
use App\Entity\User;
use App\Factory\FlatModelFactory;
use App\Factory\ReviewFactory;
use App\Model\Agency\Flat as FlatAgency;
use App\Model\Branch\Flat as FlatBranch;
use App\Model\Comment\Flat as FlatComment;
use App\Model\Property\Flat as FlatProperty;
use App\Model\Review\View;
use App\Model\SubmitReviewInput;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @covers \App\Factory\ReviewFactory
 */
class ReviewFactoryTest extends TestCase
{
    use ProphecyTrait;

    private ReviewFactory $reviewFactory;

    private $flatModelFactory;

    public function setUp(): void
    {
        $this->flatModelFactory = $this->prophesize(FlatModelFactory::class);

        $this->reviewFactory = new ReviewFactory(
            $this->flatModelFactory->reveal()
        );
    }

    /**
     * @covers \App\Factory\ReviewFactory::createViewFromEntity
     */
    public function testCreateViewFromEntity(): void
    {
        $branch = (new Branch());
        $agency = (new Agency())->addBranch($branch);
        $property = (new Property());
        $comment = (new ReviewComment())->setPublished(true);

        $flatBranch = (new FlatBranch('branchslug', 'Test Branch Name'));
        $flatAgency = (new FlatAgency('agencyslug', 'Test Agency Name'));
        $flatProperty = (new FlatProperty('propertyslug', '123 Test Street', 'CB4 3LF'));
        $flatComment = (new FlatComment(77, 'Beatrice Whisk', 'We are glad you liked it', new DateTime()));

        $this->flatModelFactory->getAgencyFlatModel($agency)
            ->shouldBeCalledOnce()
            ->willReturn($flatAgency);
        $this->flatModelFactory->getBranchFlatModel($branch)
            ->shouldBeCalledOnce()
            ->willReturn($flatBranch);
        $this->flatModelFactory->getPropertyFlatModel($property)
            ->shouldBeCalledOnce()
            ->willReturn($flatProperty);
        $this->flatModelFactory->getCommentFlatModel($comment)
            ->shouldBeCalledOnce()
            ->willReturn($flatComment);

        $review = (new Review())
            ->setBranch($branch)
            ->setProperty($property)
            ->setIdForTest(789)
            ->setAuthor('Gina Gee')
            ->setTitle('Test Title')
            ->setContent('I lived here, it was nice.')
            ->setOverallStars(4)
            ->setLandlordStars(3)
            ->setAgencyStars(null)
            ->setPropertyStars(5)
            ->setCreatedAt(new DateTime('2020-02-02 12:00:00'))
            ->addComment($comment)
        ;

        $view = $this->reviewFactory->createViewFromEntity($review);

        $this->assertEquals('Test Branch Name', $view->getBranch()->getName());
        $this->assertEquals('Test Agency Name', $view->getAgency()->getName());
        $this->assertNull($view->getAgency()->getLogoImageFilename());
        $this->assertEquals('123 Test Street', $view->getProperty()->getAddressLine1());
        $this->assertEquals(789, $view->getId());
        $this->assertEquals('Gina Gee', $view->getAuthor());
        $this->assertEquals('Test Title', $view->getTitle());
        $this->assertEquals('I lived here, it was nice.', $view->getContent());
        $this->assertEquals(4, $view->getStars()->getOverall());
        $this->assertEquals(3, $view->getStars()->getLandlord());
        $this->assertNull($view->getStars()->getAgency());
        $this->assertEquals(5, $view->getStars()->getProperty());
        $this->assertEquals('2020-02-02', $view->getCreatedAt()->format('Y-m-d'));
        $this->assertCount(1, $view->getComments());
        $this->assertEquals('We are glad you liked it', $view->getComments()[0]->getContent());
    }

    /**
     * @covers \App\Factory\ReviewFactory::createGroup
     */
    public function testCreateGroup1(): void
    {
        $property = $this->prophesize(Property::class);
        $flatProperty = $this->prophesize(FlatProperty::class);

        $review1 = $this->prophesizeEmptyReview($property);
        $review2 = $this->prophesizeEmptyReview($property);

        $reviewEntities = [$review1->reveal(), $review2->reveal()];

        $this->flatModelFactory->getPropertyFlatModel($property)
            ->shouldBeCalledTimes(2)
            ->willReturn($flatProperty);

        $output = $this->reviewFactory->createGroup('Latest Reviews', $reviewEntities);

        $this->assertEquals('Latest Reviews', $output->getTitle());
        $this->assertInstanceOf(View::class, $output->getReviews()[0]);
        $this->assertInstanceOf(View::class, $output->getReviews()[1]);
    }

    /**
     * @covers \App\Factory\ReviewFactory::createEntity
     */
    public function testCreateEntity1(): void
    {
        $branch = $this->prophesize(Branch::class);
        $user = $this->prophesize(User::class);
        $property = $this->prophesize(Property::class);
        $input = $this->prophesize(SubmitReviewInput::class);

        $input->getReviewerName()
            ->shouldBeCalledOnce()
            ->willReturn('Jo Smith');

        $input->getReviewTitle()
            ->shouldBeCalledOnce()
            ->willReturn('Nice tenancy');

        $input->getReviewContent()
            ->shouldBeCalledOnce()
            ->willReturn('It was pleasurable living here, except one time the pipes froze');

        $input->getOverallStars()
            ->shouldBeCalledOnce()
            ->willReturn(5);

        $input->getAgencyStars()
            ->shouldBeCalledOnce()
            ->willReturn(4);

        $input->getLandlordStars()
            ->shouldBeCalledOnce()
            ->willReturn(null);

        $input->getPropertyStars()
            ->shouldBeCalledOnce()
            ->willReturn(3);

        $entity = $this->reviewFactory->createEntity(
            $input->reveal(),
            $property->reveal(),
            $branch->reveal(),
            $user->reveal()
        );

        $this->assertEquals('Jo Smith', $entity->getAuthor());
        $this->assertEquals('Nice tenancy', $entity->getTitle());
        $this->assertEquals('It was pleasurable living here, except one time the pipes froze', $entity->getContent());
        $this->assertEquals(5, $entity->getOverallStars());
        $this->assertEquals(4, $entity->getAgencyStars());
        $this->assertNull($entity->getLandlordStars());
        $this->assertEquals(3, $entity->getPropertyStars());
    }

    private function prophesizeEmptyReview(ObjectProphecy $property): ObjectProphecy
    {
        $review = $this->prophesize(Review::class);
        $review->getId()->willReturn(1);
        $review->getProperty()->willReturn($property);
        $review->getCreatedAt()->willReturn(new DateTime('2020-02-02 12:00:00'));
        $review->getPublishedComments()->willReturn(new ArrayCollection());
        $review->getAgency()->willReturn(null);
        $review->getBranch()->willReturn(null);
        $review->getOverallStars()->willReturn(null);
        $review->getAgencyStars()->willReturn(null);
        $review->getPropertyStars()->willReturn(null);
        $review->getLandlordStars()->willReturn(null);
        $review->getAuthor()->willReturn(null);
        $review->getTitle()->willReturn(null);
        $review->getContent()->willReturn(null);

        return $review;
    }
}
