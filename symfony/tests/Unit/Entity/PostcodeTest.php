<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Locale;
use App\Entity\Postcode;

/**
 * @covers \App\Entity\Postcode
 */
class PostcodeTest extends AbstractEntityTestCase
{
    /**
     * @covers \App\Entity\Postcode::__toString
     */
    public function testToString1(): void
    {
        $entity = $this->getEntity();
        $entity->setPostcode('CB4 3LF');
        $this->assertEquals('CB4 3LF', (string) $entity);
    }

    /**
     * @covers \App\Entity\Postcode::getPostcode
     */
    public function testGetPostcode1(): void
    {
        $entity = $this->getEntity();
        $entity->setPostcode('CB4 3LF');
        $this->assertEquals('CB4 3LF', $entity->getPostcode());
    }

    /**
     * @covers \App\Entity\Postcode::getLocales
     */
    public function testGetLocales1(): void
    {
        $entity = $this->getEntity();
        $this->assertEmpty($entity->getLocales());

        $locale1 = (new Locale())->setName('Shoreditch');
        $entity->addLocale($locale1);
        $this->assertCount(1, $entity->getLocales());

        $locale2 = (new Locale())->setName('Clerkenwell');
        $entity->addLocale($locale2);
        $this->assertCount(2, $entity->getLocales());

        $entity->addLocale($locale1);
        $this->assertCount(2, $entity->getLocales());
    }

    protected function getEntity(): Postcode
    {
        return new Postcode();
    }
}
