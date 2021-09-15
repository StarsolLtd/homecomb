<?php

namespace App\Tests\Unit\Entity;

use App\Entity\User;

/**
 * @covers \App\Entity\User
 */
class UserTest extends AbstractEntityTestCase
{
    protected array $values = [
        'email' => 'jack@starsol.co.uk',
        'title' => 'Mr',
        'firstName' => 'Jack',
        'lastName' => 'Parnell',
        'googleId' => 'test-google-id',
    ];

    protected function getEntity(): User
    {
        $entity = new User();
        $entity = $this->setPropertiesFromValuesArray($entity);
        assert($entity instanceof User);

        return $entity;
    }
}
