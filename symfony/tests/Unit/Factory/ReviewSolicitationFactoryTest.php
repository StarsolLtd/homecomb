<?php

namespace App\Tests\Unit\Factory;

use App\Entity\Agency;
use App\Entity\Branch;
use App\Entity\Property;
use App\Entity\User;
use App\Factory\FlatModelFactory;
use App\Factory\ReviewSolicitationFactory;
use App\Model\Agency\Flat as FlatAgency;
use App\Model\Branch\Flat as FlatBranch;
use App\Model\ReviewSolicitation\CreateReviewSolicitationInput;
use App\Repository\BranchRepository;
use App\Repository\PropertyRepository;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

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

    public function testCreateEntityFromInput(): void
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

    public function testCreateFormDataModelFromUser(): void
    {
        $branch1 = (new Branch())->setSlug('branch1');
        $branch2 = (new Branch())->setSlug('branch2');
        $agency = (new Agency())->addBranch($branch1)->addBranch($branch2);
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
        $this->assertCount(2, $formData->getBranches());
    }
}
