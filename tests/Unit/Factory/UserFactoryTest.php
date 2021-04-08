<?php

namespace App\Tests\Unit\Factory;

use App\Entity\User;
use App\Factory\UserFactory;
use App\Model\User\RegisterInput;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFactoryTest extends TestCase
{
    use ProphecyTrait;

    private UserFactory $userFactory;

    private $userPasswordEncoder;

    public function setUp(): void
    {
        $this->userPasswordEncoder = $this->prophesize(UserPasswordEncoderInterface::class);

        $this->userFactory = new UserFactory(
            $this->userPasswordEncoder->reveal()
        );
    }

    public function testCreateEntityFromRegisterInput(): void
    {
        $input = new RegisterInput(
            'test.register@starsol.co.uk',
            'Testa',
            'Registrova',
            'Password_1'
        );

        $this->userPasswordEncoder->encodePassword(Argument::type(User::class), 'Password_1')
            ->shouldBeCalledOnce()
            ->willReturn('encoded-password');

        $user = $this->userFactory->createEntityFromRegisterInput($input);

        $this->assertEquals('test.register@starsol.co.uk', $user->getEmail());
        $this->assertEquals('test.register@starsol.co.uk', $user->getUsername());
        $this->assertEquals('Testa', $user->getFirstName());
        $this->assertEquals('Registrova', $user->getLastName());
        $this->assertEquals('encoded-password', $user->getPassword());
    }
}
