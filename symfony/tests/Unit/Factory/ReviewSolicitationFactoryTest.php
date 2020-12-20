<?php

namespace App\Tests\Unit\Factory;

use App\Entity\Agency;
use App\Entity\Branch;
use App\Entity\Property;
use App\Entity\User;
use App\Factory\ReviewSolicitationFactory;
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

    public function setUp(): void
    {
        $this->branchRepository = $this->prophesize(BranchRepository::class);
        $this->propertyRepository = $this->prophesize(PropertyRepository::class);

        $this->reviewSolicitationFactory = new ReviewSolicitationFactory(
            $this->branchRepository->reveal(),
            $this->propertyRepository->reveal()
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
}
