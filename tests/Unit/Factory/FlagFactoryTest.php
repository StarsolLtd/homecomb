<?php

namespace App\Tests\Unit\Factory;

use App\Entity\Agency;
use App\Entity\Branch;
use App\Entity\Flag\AgencyFlag;
use App\Entity\Flag\BranchFlag;
use App\Entity\Flag\PropertyFlag;
use App\Entity\Flag\TenancyReviewFlag;
use App\Entity\Property;
use App\Entity\TenancyReview;
use App\Entity\User;
use App\Exception\UnexpectedValueException;
use App\Factory\FlagFactory;
use App\Model\Flag\SubmitInputInterface;
use App\Repository\AgencyRepositoryInterface;
use App\Repository\BranchRepositoryInterface;
use App\Repository\PropertyRepository;
use App\Repository\TenancyReviewRepository;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @covers \App\Factory\FlagFactory
 */
final class FlagFactoryTest extends TestCase
{
    use ProphecyTrait;

    private FlagFactory $flagFactory;

    private ObjectProphecy $agencyRepository;
    private ObjectProphecy $branchRepository;
    private ObjectProphecy $propertyRepository;
    private ObjectProphecy $reviewRepository;

    public function setUp(): void
    {
        $this->agencyRepository = $this->prophesize(AgencyRepositoryInterface::class);
        $this->branchRepository = $this->prophesize(BranchRepositoryInterface::class);
        $this->propertyRepository = $this->prophesize(PropertyRepository::class);
        $this->reviewRepository = $this->prophesize(TenancyReviewRepository::class);

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
        $input = $this->prophesize(SubmitInputInterface::class);
        $input->getEntityId()->shouldBeCalledOnce()->willReturn(789);
        $input->getEntityName()->shouldBeCalledOnce()->willReturn('Review');
        $input->getContent()->shouldBeCalledOnce()->willReturn('This is spam');

        $user = $this->prophesize(User::class);
        $tenancyReview = $this->prophesize(TenancyReview::class);

        $this->reviewRepository->findOnePublishedById(789)
            ->shouldBeCalledOnce()
            ->willReturn($tenancyReview);

        /** @var TenancyReviewFlag $flag */
        $flag = $this->flagFactory->createEntityFromSubmitInput($input->reveal(), $user->reveal());

        $this->assertInstanceOf(TenancyReviewFlag::class, $flag);
        $this->assertEquals($tenancyReview->reveal(), $flag->getTenancyReview());
        $this->assertEquals('This is spam', $flag->getContent());
        $this->assertEquals($user->reveal(), $flag->getUser());
    }

    /**
     * @covers \App\Factory\FlagFactory::createEntityFromSubmitInput
     * Test create AgencyFlag
     */
    public function testCreateEntityFromSubmitInput2(): void
    {
        $input = $this->prophesize(SubmitInputInterface::class);
        $input->getEntityId()->shouldBeCalledOnce()->willReturn(789);
        $input->getEntityName()->shouldBeCalledOnce()->willReturn('Agency');
        $input->getContent()->shouldBeCalledOnce()->willReturn('Not a real agency');

        $user = $this->prophesize(User::class);
        $agency = $this->prophesize(Agency::class);

        $this->agencyRepository->findOnePublishedById(789)
            ->shouldBeCalledOnce()
            ->willReturn($agency);

        /** @var AgencyFlag $flag */
        $flag = $this->flagFactory->createEntityFromSubmitInput($input->reveal(), $user->reveal());

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
        $input = $this->prophesize(SubmitInputInterface::class);
        $input->getEntityId()->shouldBeCalledOnce()->willReturn(789);
        $input->getEntityName()->shouldBeCalledOnce()->willReturn('Branch');
        $input->getContent()->shouldBeCalledOnce()->willReturn('Agency does not have a branch here');

        $user = $this->prophesize(User::class);
        $branch = $this->prophesize(Branch::class);

        $this->branchRepository->findOnePublishedById(789)
            ->shouldBeCalledOnce()
            ->willReturn($branch);

        /** @var BranchFlag $flag */
        $flag = $this->flagFactory->createEntityFromSubmitInput($input->reveal(), $user->reveal());

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
        $input = $this->prophesize(SubmitInputInterface::class);
        $input->getEntityId()->shouldBeCalledOnce()->willReturn(789);
        $input->getEntityName()->shouldBeCalledOnce()->willReturn('Property');
        $input->getContent()->shouldBeCalledOnce()->willReturn('Not a real agency');

        $user = $this->prophesize(User::class);
        $property = $this->prophesize(Property::class);

        $this->propertyRepository->findOnePublishedById(789)
            ->shouldBeCalledOnce()
            ->willReturn($property);

        /** @var PropertyFlag $flag */
        $flag = $this->flagFactory->createEntityFromSubmitInput($input->reveal(), $user->reveal());

        $this->assertInstanceOf(PropertyFlag::class, $flag);
        $this->assertEquals($property->reveal(), $flag->getProperty());
        $this->assertEquals('Not a real agency', $flag->getContent());
        $this->assertEquals($user->reveal(), $flag->getUser());
    }

    /**
     * @covers \App\Factory\FlagFactory::createEntityFromSubmitInput
     * Test throws UnexpectedValueException when entity name not supported
     */
    public function testCreateEntityFromSubmitInput5(): void
    {
        $input = $this->prophesize(SubmitInputInterface::class);
        $input->getEntityId()->shouldBeCalledOnce()->willReturn(789);
        $input->getEntityName()->shouldBeCalledOnce()->willReturn('Chopsticks');

        $this->expectException(UnexpectedValueException::class);

        $this->flagFactory->createEntityFromSubmitInput($input->reveal(), null);
    }
}
