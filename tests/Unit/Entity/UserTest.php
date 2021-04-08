<?php

namespace App\Tests\Unit\Entity;

use App\Entity\User;

/**
 * @covers \App\Entity\User
 */
class UserTest extends AbstractEntityTestCase
{
    protected function getEntity(): User
    {
        return new User();
    }
}
