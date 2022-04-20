<?php

namespace App\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use ReflectionClass;

abstract class AbstractEntityTestCase extends TestCase
{
    protected array $values = [];

    abstract protected function getEntity(): object;

    public function testGetId1(): void
    {
        $entity = $this->getEntity();
        $this->setId($entity, 789);
        $this->assertEquals(789, $entity->getId());
    }

    public function testEntityData1(): void
    {
        $entity = $this->getEntity();
        foreach ($this->values as $property => $value) {
            $getter = (is_bool($value) ? 'is' : 'get').ucfirst($property);
            self::assertTrue(method_exists($entity, $getter));
            $expected = $this->values[$property];
            $actual = $entity->$getter();
            self::assertSame($expected, $actual);
        }
    }

    protected function setId($entity, $id): void
    {
        $class = new ReflectionClass($entity);
        $property = $class->getProperty('id');
        $property->setAccessible(true);

        $property->setValue($entity, $id);
    }

    protected function setPropertiesFromValuesArray(object $entity): object
    {
        foreach ($this->values as $property => $value) {
            $setterName = 'set'.ucfirst($property);
            $entity->$setterName($value);
        }

        return $entity;
    }
}
