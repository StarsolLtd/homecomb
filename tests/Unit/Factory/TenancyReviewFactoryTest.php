<?php

namespace App\Tests\Unit\Factory;

use App\Entity\Agency;
use App\Entity\Branch;
use App\Entity\Comment\TenancyReviewComment;
use App\Entity\Property;
use App\Entity\TenancyReview;
use App\Entity\User;
use App\Entity\Vote\TenancyReviewVote;
use App\Factory\FlatModelFactory;
use App\Factory\TenancyReviewFactory;
use App\Model\Agency\Flat as FlatAgency;
use App\Model\Branch\Flat as FlatBranch;
use App\Model\Comment\Flat as FlatComment;
use App\Model\Property\Flat as FlatProperty;
use App\Model\TenancyReview\SubmitInputInterface;
use App\Model\TenancyReview\View;
use App\Tests\Unit\SetIdByReflectionTrait;
use App\Util\ReviewHelper;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @covers \App\Factory\TenancyReviewFactory
 */
final class TenancyReviewFactoryTest extends TestCase
{
    use ProphecyTrait;
    use SetIdByReflectionTrait;

    private TenancyReviewFactory $tenancyReviewFactory;

    private ObjectProphecy $flatModelFactory;
    private ObjectProphecy $reviewHelper;

    public function setUp(): void
    {
        $this->flatModelFactory = $this->prophesize(FlatModelFactory::class);
        $this->reviewHelper = $this->prophesize(ReviewHelper::class);

        $this->tenancyReviewFactory = new TenancyReviewFactory(
            $this->flatModelFactory->reveal(),
            $this->reviewHelper->reveal(),
        );
    }

    /**
     * @covers \App\Factory\TenancyReviewFactory::createViewFromEntity
     */
    public function testCreateViewFromEntity(): void
    {
        $branch = (new Branch());
        $agency = (new Agency())->addBranch($branch);
        $property = (new Property());
        $comment = (new TenancyReviewComment())->setPublished(true);

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

        $positiveVote = (new TenancyReviewVote())->setPositive(true);

        $tenancyReview = (new TenancyReview())
            ->setBranch($branch)
            ->setProperty($property)
            ->setAuthor('Gina Gee')
            ->setStart(new DateTime('2018-03-01'))
            ->setEnd(new DateTime('2020-10-01'))
            ->setTitle('Test Title')
            ->setContent('I lived here, it was nice.')
            ->setOverallStars(4)
            ->setLandlordStars(3)
            ->setAgencyStars(null)
            ->setPropertyStars(5)
            ->setCreatedAt(new DateTime('2020-02-02 12:00:00'))
            ->addComment($comment)
            ->addVote($positiveVote)
        ;
        $this->setIdByReflection($tenancyReview, 789);

        $view = $this->tenancyReviewFactory->createViewFromEntity($tenancyReview);

        $this->assertEquals('Test Branch Name', $view->getBranch()->getName());
        $this->assertEquals('Test Agency Name', $view->getAgency()->getName());
        $this->assertNull($view->getAgency()->getLogoImageFilename());
        $this->assertEquals('123 Test Street', $view->getProperty()->getAddressLine1());
        $this->assertEquals(789, $view->getId());
        $this->assertEquals('Gina Gee', $view->getAuthor());
        $this->assertEquals('2018-03-01', $view->getStart()->format('Y-m-d'));
        $this->assertEquals('2020-10-01', $view->getEnd()->format('Y-m-d'));
        $this->assertEquals('Test Title', $view->getTitle());
        $this->assertEquals('I lived here, it was nice.', $view->getContent());
        $this->assertEquals(4, $view->getStars()->getOverall());
        $this->assertEquals(3, $view->getStars()->getLandlord());
        $this->assertNull($view->getStars()->getAgency());
        $this->assertEquals(5, $view->getStars()->getProperty());
        $this->assertEquals('2020-02-02', $view->getCreatedAt()->format('Y-m-d'));
        $this->assertCount(1, $view->getComments());
        $this->assertEquals('We are glad you liked it', $view->getComments()[0]->getContent());
        $this->assertEquals(1, $view->getPositiveVotes());
        $this->assertEquals(0, $view->getNegativeVotes());
        $this->assertEquals(1, $view->getVotesScore());
    }

    /**
     * @covers \App\Factory\TenancyReviewFactory::createGroup
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

        $output = $this->tenancyReviewFactory->createGroup('Latest Reviews', $reviewEntities);

        $this->assertEquals('Latest Reviews', $output->getTitle());
        $this->assertInstanceOf(View::class, $output->getTenancyReviews()[0]);
        $this->assertInstanceOf(View::class, $output->getTenancyReviews()[1]);
    }

    /**
     * @covers \App\Factory\TenancyReviewFactory::createEntity
     */
    public function testCreateEntity1(): void
    {
        $branch = $this->prophesize(Branch::class);
        $user = $this->prophesize(User::class);
        $property = $this->prophesize(Property::class);
        $input = $this->prophesize(SubmitInputInterface::class);

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

        $input->getStart()
            ->shouldBeCalledOnce()
            ->willReturn('2019-06-01');

        $input->getEnd()
            ->shouldBeCalledOnce()
            ->willReturn('2020-12-01');

        $this->reviewHelper->generateTenancyReviewSlug(Argument::type(TenancyReview::class))
            ->shouldBeCalledOnce()
            ->willReturn('test-tr-slug');

        $entity = $this->tenancyReviewFactory->createEntity(
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
        $this->assertEquals('2019-06-01', $entity->getStart()->format('Y-m-d'));
        $this->assertEquals('2020-12-01', $entity->getEnd()->format('Y-m-d'));
        $this->assertEquals('test-tr-slug', $entity->getSlug());
    }

    private function prophesizeEmptyReview(ObjectProphecy $property): ObjectProphecy
    {
        $tenancyReview = $this->prophesize(TenancyReview::class);
        $tenancyReview->getId()->willReturn(1);
        $tenancyReview->getProperty()->willReturn($property);
        $tenancyReview->getCreatedAt()->willReturn(new DateTime('2020-02-02 12:00:00'));
        $tenancyReview->getPublishedComments()->willReturn(new ArrayCollection());
        $tenancyReview->getAgency()->willReturn(null);
        $tenancyReview->getBranch()->willReturn(null);
        $tenancyReview->getOverallStars()->willReturn(null);
        $tenancyReview->getAgencyStars()->willReturn(null);
        $tenancyReview->getPropertyStars()->willReturn(null);
        $tenancyReview->getLandlordStars()->willReturn(null);
        $tenancyReview->getAuthor()->willReturn(null);
        $tenancyReview->getStart()->willReturn(null);
        $tenancyReview->getEnd()->willReturn(null);
        $tenancyReview->getTitle()->willReturn(null);
        $tenancyReview->getContent()->willReturn(null);
        $tenancyReview->getPositiveVotesCount()->willReturn(0);
        $tenancyReview->getNegativeVotesCount()->willReturn(0);
        $tenancyReview->getVotesScore()->willReturn(0);

        return $tenancyReview;
    }
}
