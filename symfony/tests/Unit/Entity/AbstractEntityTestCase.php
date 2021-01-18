<?php

namespace App\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use ReflectionClass;

abstract class AbstractEntityTestCase extends TestCase
{
    protected function setId($entity, $id)
    {
        $class = new ReflectionClass($entity);
        $property = $class->getProperty('id');
        $property->setAccessible(true);

        $property->setValue($entity, $id);
    }
}
