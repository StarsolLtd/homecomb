<?php

namespace App\Tests\Unit\Factory;

use App\Entity\Agency;
use App\Entity\Branch;
use App\Entity\Flag\AgencyFlag;
use App\Entity\Flag\BranchFlag;
use App\Entity\Flag\PropertyFlag;
use App\Entity\Flag\ReviewFlag;
use App\Entity\Property;
use App\Entity\Review;
use App\Entity\User;
use App\Exception\UnexpectedValueException;
use App\Factory\FlagFactory;
use App\Model\Flag\SubmitInput;
use App\Repository\AgencyRepository;
use App\Repository\BranchRepository;
use App\Repository\PropertyRepository;
use App\Repository\ReviewRepository;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @covers \App\Factory\FlagFactory
 */
class FlagFactoryTest extends TestCase
{
    use ProphecyTrait;

    private FlagFactory $flagFactory;

    private $agencyRepository;
    private $branchRepository;
    private $propertyRepository;
    private $reviewRepository;

    public function setUp(): void
    {
        $this->agencyRepository = $this->prophesize(AgencyRepository::class);
        $this->branchRepository = $this->prophesize(BranchRepository::class);
        $this->propertyRepository = $this->prophesize(PropertyRepository::class);
        $this->reviewRepository = $this->prophesize(ReviewRepository::class);

        $this->flagFactory = new FlagFactory(
            $this->agencyRepository->reveal(),
            $this->branchRepository->reveal(),
            $this->propertyRepository->reveal(),
            $this->reviewRepository->reveal(),
        );
    }

    /**
     * @covers \App\Factory\FlagFactory::createEntityFromSubmitInput
     * Test create ReviewFlag
     */
    public function testCreateEntityFromSubmitInput1(): void
    {
        $input = new SubmitInput('Review', 789, 'This is spam');

        $user = $this->prophesize(User::class);
        $review = $this->prophesize(Review::class);

        $this->reviewRepository->findOnePublishedById($input->getEntityId())
            ->shouldBeCalledOnce()
            ->willReturn($review);

        /** @var ReviewFlag $flag */
        $flag = $this->flagFactory->createEntityFromSubmitInput($input, $user->reveal());

        $this->assertInstanceOf(ReviewFlag::class, $flag);
        $this->assertEquals($review->reveal(), $flag->getReview());
        $this->assertEquals('This is spam', $flag->getContent());
        $this->assertEquals($user->reveal(), $flag->getUser());
    }

    /**
     * @covers \App\Factory\FlagFactory::createEntityFromSubmitInput
     * Test create AgencyFlag
     */
    public function testCreateEntityFromSubmitInput2(): void
    {
        $input = new SubmitInput('Agency', 789, 'Not a real agency');

        $user = $this->prophesize(User::class);
        $agency = $this->prophesize(Agency::class);

        $this->agencyRepository->findOnePublishedById($input->getEntityId())
            ->shouldBeCalledOnce()
            ->willReturn($agency);

        /** @var AgencyFlag $flag */
        $flag = $this->flagFactory->createEntityFromSubmitInput($input, $user->reveal());

        $this->assertInstanceOf(AgencyFlag::class, $flag);
        $this->assertEquals($agency->reveal(), $flag->getAgency());
        $this->assertEquals('Not a real agency', $flag->getContent());
        $this->assertEquals($user->reveal(), $flag->getUser());
    }

    /**
     * @covers \App\Factory\FlagFactory::createEntityFromSubmitInput
     * Test create BranchFlag
     */
    public function testCreateEntityFromSubmitInput3(): void
    {
        $input = new SubmitInput('Branch', 789, 'Agency does not have a branch here');

        $user = $this->prophesize(User::class);
        $branch = $this->prophesize(Branch::class);

        $this->branchRepository->findOnePublishedById($input->getEntityId())
            ->shouldBeCalledOnce()
            ->willReturn($branch);

        /** @var BranchFlag $flag */
        $flag = $this->flagFactory->createEntityFromSubmitInput($input, $user->reveal());

        $this->assertInstanceOf(BranchFlag::class, $flag);
        $this->assertEquals($branch->reveal(), $flag->getBranch());
        $this->assertEquals('Agency does not have a branch here', $flag->getContent());
        $this->assertEquals($user->reveal(), $flag->getUser());
    }

    /**
     * @covers \App\Factory\FlagFactory::createEntityFromSubmitInput
     * Test create PropertyFlag
     */
    public function testCreateEntityFromSubmitInput4(): void
    {
        $input = new SubmitInput('Property', 789, 'This property does not exist');

        $user = $this->prophesize(User::class);
        $property = $this->prophesize(Property::class);

        $this->propertyRepository->findOnePublishedById($input->getEntityId())
            ->shouldBeCalledOnce()
            ->willReturn($property);

        /** @var PropertyFlag $flag */
        $flag = $this->flagFactory->createEntityFromSubmitInput($input, $user->reveal());

        $this->assertInstanceOf(PropertyFlag::class, $flag);
        $this->assertEquals($property->reveal(), $flag->getProperty());
        $this->assertEquals('This property does not exist', $flag->getContent());
        $this->assertEquals($user->reveal(), $flag->getUser());
    }

    /**
     * @covers \App\Factory\FlagFactory::createEntityFromSubmitInput
     * Test throws UnexpectedValueException when entity name not supported
     */
    public function testCreateEntityFromSubmitInput5(): void
    {
        $input = new SubmitInput('Chopsticks', 789, 'I find a fork easier');

        $this->expectException(UnexpectedValueException::class);

        $this->flagFactory->createEntityFromSubmitInput($input, null);
    }
}
