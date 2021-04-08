<?php

namespace App\Tests\Unit\Factory;

use App\Entity\Email;
use App\Entity\User;
use App\Factory\EmailFactory;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @covers \App\Factory\EmailFactory
 */
class EmailFactoryTest extends TestCase
{
    use ProphecyTrait;

    private EmailFactory $emailFactory;

    public function setUp(): void
    {
        $this->emailFactory = new EmailFactory();
    }

    /**
     * @covers \App\Factory\EmailFactory::createEntity
     */
    public function testCreateEntity1(): void
    {
        $senderUser = $this->prophesize(User::class);
        $recipientUser = $this->prophesize(User::class);
        $resendOfEmail = $this->prophesize(Email::class);

        $entity = $this->emailFactory->createEntity(
            'HomeComb <mailer@homecomb.co.uk>',
            'Jack Harper <jack.harper@starsol.co.uk>',
            'How would you like to receive an email solely for the purposes of testing? Well today is your lucky day!',
            'Sample body',
            '<span>Sample body</span>',
            5678,
            $senderUser->reveal(),
            $recipientUser->reveal(),
            $resendOfEmail->reveal()
        );

        $this->assertEquals('HomeComb <mailer@homecomb.co.uk>', $entity->getSender());
        $this->assertEquals('Jack Harper <jack.harper@starsol.co.uk>', $entity->getRecipient());
        $this->assertEquals('How would you like to receive an email solely for the purposes of testing? Well today is your lucky day!', $entity->getSubject());
        $this->assertEquals('Sample body', $entity->getText());
        $this->assertEquals('<span>Sample body</span>', $entity->getHtml());
        $this->assertEquals(5678, $entity->getType());
        $this->assertEquals($senderUser->reveal(), $entity->getSenderUser());
        $this->assertEquals($recipientUser->reveal(), $entity->getRecipientUser());
        $this->assertEquals($resendOfEmail->reveal(), $entity->getResendOfEmail());
        $this->assertNull($entity->getSentAt());
    }
}
