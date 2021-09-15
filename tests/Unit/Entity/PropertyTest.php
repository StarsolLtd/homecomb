<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Property;
use App\Entity\TenancyReview;

/**
 * @covers \App\Entity\Property
 */
class PropertyTest extends AbstractEntityTestCase
{
    protected array $values = [
        'addressLine1' => 'Test Address Line 1',
        'addressLine2' => 'Test Address Line 2',
        'addressLine3' => 'Test Address Line 3',
        'addressLine4' => 'Test Address Line 4',
        'locality' => 'Test Locality',
        'addressCity' => 'Test Address City',
        'county' => 'Test County',
        'postcode' => 'Test Postcode',
        'addressDistrict' => 'Test Address District',
        'thoroughfare' => 'Test Thoroughfare',
        'countryCode' => 'UK',
        'latitude' => 2.34,
        'longitude' => 5.67,
        'slug' => 'test-property-slug',
        'published' => true,
    ];

    /**
     * @covers \App\Entity\Property::__toString
     */
    public function testToString1(): void
    {
        $entity = $this->getEntity()
            ->setAddressLine1('249 Victoria Road')
            ->setPostcode('CB4 3LF');

        $this->assertEquals('249 Victoria Road, CB4 3LF', (string) $entity);
    }

    /**
     * @covers \App\Entity\Property::getPublishedTenancyReviews
     */
    public function testGetPublishedTenancyReviews1(): void
    {
        $entity = $this->getEntity();
        $this->assertEmpty($entity->getPublishedTenancyReviews());

        $reviewA = (new TenancyReview())->setPublished(true)->setTitle('A');
        $entity->addTenancyReview($reviewA);
        $this->assertCount(1, $entity->getPublishedTenancyReviews());

        $reviewB = (new TenancyReview())->setPublished(false)->setTitle('B');
        $entity->addTenancyReview($reviewB);
        $this->assertCount(1, $entity->getPublishedTenancyReviews());

        $reviewC = (new TenancyReview())->setPublished(true)->setTitle('C');
        $entity->addTenancyReview($reviewC);
        $this->assertCount(2, $entity->getPublishedTenancyReviews());

        $entity->addTenancyReview($reviewC);
        $this->assertCount(2, $entity->getPublishedTenancyReviews());
    }

    protected function getEntity(): Property
    {
        $entity = new Property();

        $entity = $this->setPropertiesFromValuesArray($entity);
        assert($entity instanceof Property);

        return $entity;
    }
}
