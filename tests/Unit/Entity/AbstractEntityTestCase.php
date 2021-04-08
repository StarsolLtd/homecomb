<?php

namespace App\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use ReflectionClass;

abstract class AbstractEntityTestCase extends TestCase
{
    abstract protected function getEntity(): object;

    /**
     * @covers \App\Entity\Postcode::getId
     */
    public function testGetId1(): void
    {
        $entity = $this->getEntity();
        $this->setId($entity, 789);
        $this->assertEquals(789, $entity->getId());
    }

    protected function setId($entity, $id)
    {
        $class = new ReflectionClass($entity);
        $property = $class->getProperty('id');
        $property->setAccessible(true);

        $property->setValue($entity, $id);
    }
}
