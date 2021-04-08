<?php

namespace App\Tests\Unit\Factory;

use App\Entity\Agency;
use App\Entity\Branch;
use App\Entity\Property;
use App\Entity\ReviewSolicitation;
use App\Entity\User;
use App\Exception\DeveloperException;
use App\Exception\NotFoundException;
use App\Factory\FlatModelFactory;
use App\Factory\ReviewSolicitationFactory;
use App\Model\Agency\Flat as FlatAgency;
use App\Model\Branch\Flat as FlatBranch;
use App\Model\Property\Flat as FlatProperty;
use App\Model\ReviewSolicitation\CreateReviewSolicitationInput;
use App\Repository\BranchRepository;
use App\Repository\PropertyRepository;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @covers \App\Factory\ReviewSolicitationFactory
 */
class ReviewSolicitationFactoryTest extends TestCase
{
    use ProphecyTrait;

    private ReviewSolicitationFactory $reviewSolicitationFactory;

    private $branchRepository;
    private $propertyRepository;
    private $flatModelFactory;

    public function setUp(): void
    {
        $this->branchRepository = $this->prophesize(BranchRepository::class);
        $this->propertyRepository = $this->prophesize(PropertyRepository::class);
        $this->flatModelFactory = $this->prophesize(FlatModelFactory::class);

        $this->reviewSolicitationFactory = new ReviewSolicitationFactory(
            $this->branchRepository->reveal(),
            $this->propertyRepository->reveal(),
            $this->flatModelFactory->reveal()
        );
    }

    /**
     * @covers \App\Factory\ReviewSolicitationFactory::createEntityFromInput
     */
    public function testCreateEntityFromInput1(): void
    {
        $input = new CreateReviewSolicitationInput(
            'branchslug',
            'propertyslug',
            null,
            'Jack',
            'Harper',
            'jack.harper@starsol.co.uk',
            'SAMPLE'
        );

        $senderUser = new User();
        $agency = (new Agency())->addAdminUser($senderUser);
        $branch = (new Branch())->setAgency($agency)->setSlug('branchslug');
        $property = (new Property())->setSlug('propertyslug');

        $this->branchRepository->findOnePublishedBySlug('branchslug')
            ->shouldBeCalledOnce()
            ->willReturn($branch);

        $this->propertyRepository->findOnePublishedBySlug('propertyslug')
            ->shouldBeCalledOnce()
            ->willReturn($property);

        $entity = $this->reviewSolicitationFactory->createEntityFromInput($input, $senderUser);

        $this->assertEquals($branch, $entity->getBranch());
        $this->assertEquals($property, $entity->getProperty());
        $this->assertEquals($senderUser, $entity->getSenderUser());
        $this->assertNull($entity->getRecipientTitle());
        $this->assertEquals('Jack', $entity->getRecipientFirstName());
        $this->assertEquals('Harper', $entity->getRecipientLastName());
        $this->assertEquals('jack.harper@starsol.co.uk', $entity->getRecipientEmail());
        $this->assertEquals('fe03014bb9d8cec7c23787c86ef74eeea4878c69', $entity->getCode());
    }

    /**
     * @covers \App\Factory\ReviewSolicitationFactory::createFormDataModelFromUser
     */
    public function testCreateFormDataModelFromUser1(): void
    {
        $branch1 = (new Branch())->setSlug('branch1')->setPublished(true);
        $branch2 = (new Branch())->setSlug('branch2')->setPublished(true);
        $branch3 = (new Branch())->setSlug('branch3')->setPublished(false);
        $agency = (new Agency())->addBranch($branch1)->addBranch($branch2)->addBranch($branch3);
        $user = (new User())->setAdminAgency($agency);

        $this->flatModelFactory->getAgencyFlatModel($agency)
            ->shouldBeCalledOnce()
            ->willReturn(new FlatAgency('agencyslug', 'Test Agency'));

        $this->flatModelFactory->getBranchFlatModel($branch1)
            ->shouldBeCalledOnce()
            ->willReturn(new FlatBranch('branch1', 'Testerton'));

        $this->flatModelFactory->getBranchFlatModel($branch2)
            ->shouldBeCalledOnce()
            ->willReturn(new FlatBranch('branch2', 'Testerfield'));

        $formData = $this->reviewSolicitationFactory->createFormDataModelFromUser($user);

        $this->assertEquals('agencyslug', $formData->getAgency()->getSlug());
        $this->assertCount(2, $formData->getBranches()); // Only published branches should be shown
    }

    /**
     * @covers \App\Factory\ReviewSolicitationFactory::createFormDataModelFromUser
     * Test throws DeveloperException when user is not agency admin.
     */
    public function testCreateFormDataModelFromUser2(): void
    {
        $user = new User();

        $this->expectException(DeveloperException::class);

        $this->reviewSolicitationFactory->createFormDataModelFromUser($user);
    }

    /**
     * @covers \App\Factory\ReviewSolicitationFactory::createViewByEntity
     */
    public function testCreateViewByEntity1(): void
    {
        $property = (new Property());
        $branch = (new Branch());
        $agency = (new Agency())->addBranch($branch);

        $agencyModel = $this->prophesize(FlatAgency::class);
        $branchModel = $this->prophesize(FlatBranch::class);
        $propertyModel = $this->prophesize(FlatProperty::class);

        $rs = (new ReviewSolicitation())
            ->setCode('testcode')
            ->setBranch($branch)
            ->setProperty($property)
            ->setRecipientFirstName('Jack')
            ->setRecipientLastName('Parnell')
            ->setRecipientEmail('jack.parnell@starsol.co.uk')
        ;

        $this->flatModelFactory->getAgencyFlatModel($agency)
            ->shouldBeCalledOnce()
            ->willReturn($agencyModel);

        $this->flatModelFactory->getBranchFlatModel($branch)
            ->shouldBeCalledOnce()
            ->willReturn($branchModel);

        $this->flatModelFactory->getPropertyFlatModel($property)
            ->shouldBeCalledOnce()
            ->willReturn($propertyModel);

        $view = $this->reviewSolicitationFactory->createViewByEntity($rs);

        $this->assertEquals('testcode', $view->getCode());
        $this->assertNull($view->getReviewerTitle());
        $this->assertEquals('Jack', $view->getReviewerFirstName());
        $this->assertEquals('Parnell', $view->getReviewerLastName());
        $this->assertEquals('jack.parnell@starsol.co.uk', $view->getReviewerEmail());
    }

    /**
     * @covers \App\Factory\ReviewSolicitationFactory::createViewByEntity
     * Test throws NotFoundException when Branch has no Agency
     */
    public function testCreateViewByEntity2(): void
    {
        $property = (new Property());
        $branch = (new Branch());

        $rs = (new ReviewSolicitation())
            ->setCode('testcode')
            ->setBranch($branch)
            ->setProperty($property)
            ->setRecipientFirstName('Jack')
            ->setRecipientLastName('Parnell')
            ->setRecipientEmail('jack.parnell@starsol.co.uk')
        ;

        $this->expectException(NotFoundException::class);

        $this->reviewSolicitationFactory->createViewByEntity($rs);
    }
}
