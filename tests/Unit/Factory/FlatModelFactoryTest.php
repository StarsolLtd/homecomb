<?php

namespace App\Tests\Unit\Factory;

use App\Entity\Agency;
use App\Entity\Branch;
use App\Entity\City;
use App\Entity\Comment\TenancyReviewComment;
use App\Entity\Property;
use App\Entity\User;
use App\Factory\FlatModelFactory;
use DateTime;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @covers \App\Factory\FlashMessageFactory
 */
class FlatModelFactoryTest extends TestCase
{
    use ProphecyTrait;

    private FlatModelFactory $flatModelFactory;

    public function setUp(): void
    {
        $this->flatModelFactory = new FlatModelFactory();
    }

    /**
     * @covers \App\Factory\FlatModelFactory::getAgencyFlatModel
     */
    public function testGetAgencyFlatModel(): void
    {
        $agency = (new Agency())
            ->setSlug('agencyslug')
            ->setName('Surrey Lets')
            ->setExternalUrl('http://surrey.lets')
            ->setPostcode('GU21 5SZ')
            ->setPublished(true);

        $model = $this->flatModelFactory->getAgencyFlatModel($agency);

        $this->assertEquals('agencyslug', $model->getSlug());
        $this->assertEquals('Surrey Lets', $model->getName());
        $this->assertEquals('http://surrey.lets', $model->getExternalUrl());
        $this->assertEquals('GU21 5SZ', $model->getPostcode());
        $this->assertTrue($model->isPublished());
        $this->assertNull($model->getLogoImageFilename());
    }

    /**
     * @covers \App\Factory\FlatModelFactory::getBranchFlatModel
     */
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

    /**
     * @covers \App\Factory\FlatModelFactory::getCityFlatModel
     */
    public function testGetCityFlatModel(): void
    {
        $city = $this->prophesize(City::class);

        $city->getSlug()->shouldBeCalledOnce()->willReturn('test-city-slug');
        $city->getName()->shouldBeCalledOnce()->willReturn('Colchester');
        $city->getCounty()->shouldBeCalledOnce()->willReturn('Essex');
        $city->getCountryCode()->shouldBeCalledOnce()->willReturn('UK');
        $city->isPublished()->shouldBeCalledOnce()->willReturn(true);

        $model = $this->flatModelFactory->getCityFlatModel($city->reveal());

        $this->assertEquals('test-city-slug', $model->getSlug());
        $this->assertEquals('Colchester', $model->getName());
        $this->assertEquals('Essex', $model->getCounty());
        $this->assertEquals('UK', $model->getCountryCode());
        $this->assertTrue($model->isPublished());
    }

    /**
     * @covers \App\Factory\FlatModelFactory::getCommentFlatModel
     */
    public function testGetCommentFlatModel(): void
    {
        $content = 'I am sorry to hear both of the bathroom taps were cold. The landlord has advised me that one of '
                 .'the taps will dispense hot water if you leave it running for a few seconds.';

        $createdAt = new DateTime('2021-01-08 12:34:56');

        $user = $this->prophesize(User::class);
        $comment = $this->prophesize(TenancyReviewComment::class);

        $comment->getId()->shouldBeCalledOnce()->willReturn(33);
        $comment->getContent()->shouldBeCalledOnce()->willReturn($content);
        $comment->getCreatedAt()->shouldBeCalledOnce()->willReturn($createdAt);
        $comment->getUser()->shouldBeCalledOnce()->willReturn($user);

        $user->getFirstName()->shouldBeCalledOnce()->willReturn('Cecilia');
        $user->getLastName()->shouldBeCalledOnce()->willReturn('Marina');

        $model = $this->flatModelFactory->getCommentFlatModel($comment->reveal());

        $this->assertEquals(33, $model->getId());
        $this->assertEquals('Cecilia Marina', $model->getAuthor());
        $this->assertEquals($content, $model->getContent());
        $this->assertEquals($createdAt, $model->getCreatedAt());
    }

    /**
     * @covers \App\Factory\FlatModelFactory::getPropertyFlatModel
     */
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

    /**
     * @covers \App\Factory\FlatModelFactory::getUserFlatModel
     */
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
        $this->assertFalse($model->isAgencyAdmin());
    }
}
