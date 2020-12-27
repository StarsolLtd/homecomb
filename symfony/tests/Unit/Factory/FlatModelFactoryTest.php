<?php

namespace App\Tests\Unit\Factory;

use App\Entity\Agency;
use App\Entity\Branch;
use App\Entity\Property;
use App\Entity\User;
use App\Factory\FlatModelFactory;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class FlatModelFactoryTest extends TestCase
{
    use ProphecyTrait;

    private FlatModelFactory $flatModelFactory;

    public function setUp(): void
    {
        $this->flatModelFactory = new FlatModelFactory();
    }

    public function testGetAgencyFlatModel(): void
    {
        $agency = (new Agency())
            ->setSlug('agencyslug')
            ->setName('Surrey Lets')
            ->setPublished(true);

        $model = $this->flatModelFactory->getAgencyFlatModel($agency);

        $this->assertEquals('agencyslug', $model->getSlug());
        $this->assertEquals('Surrey Lets', $model->getName());
        $this->assertTrue($model->isPublished());
        $this->assertNull($model->getLogoImageFilename());
    }

    public function testGetBranchFlatModel(): void
    {
        $branch = (new Branch())
            ->setSlug('branchslug')
            ->setName('Chessington')
            ->setPublished(true);

        $model = $this->flatModelFactory->getBranchFlatModel($branch);

        $this->assertEquals('branchslug', $model->getSlug());
        $this->assertEquals('Chessington', $model->getName());
        $this->assertTrue($model->isPublished());
        $this->assertNull($model->getTelephone());
        $this->assertNull($model->getEmail());
    }

    public function testGetPropertyFlatModel(): void
    {
        $property = (new Property())
            ->setSlug('propertyslug')
            ->setAddressLine1('28 Bateman Street')
            ->setPostcode('CB2 2TG');

        $model = $this->flatModelFactory->getPropertyFlatModel($property);

        $this->assertEquals('propertyslug', $model->getSlug());
        $this->assertEquals('28 Bateman Street', $model->getAddressLine1());
        $this->assertEquals('CB2 2TG', $model->getPostcode());
    }

    public function testGetUserFlatModel(): void
    {
        $user = (new User())
            ->setEmail('jack@starsol.co.uk')
            ->setTitle(null)
            ->setFirstName('Jack')
            ->setLastName('Parnell')
        ;

        $model = $this->flatModelFactory->getUserFlatModel($user);

        $this->assertEquals('jack@starsol.co.uk', $model->getUsername());
        $this->assertNull($model->getTitle());
        $this->assertEquals('Jack', $model->getFirstName());
        $this->assertEquals('Parnell', $model->getLastName());
    }
}
