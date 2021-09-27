<?php

namespace App\Tests\Unit\Factory\Survey;

use App\Entity\Survey\Survey;
use App\Entity\User;
use App\Factory\Survey\ResponseFactory;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @covers \App\Factory\Survey\ResponseFactory
 */
final class ResponseFactoryTest extends TestCase
{
    use ProphecyTrait;

    private ResponseFactory $responseFactory;

    public function setUp(): void
    {
        $this->responseFactory = new ResponseFactory();
    }

    /**
     * @covers \App\Factory\Survey\ResponseFactory::createEntity
     */
    public function testCreateEntity1(): void
    {
        $survey = $this->prophesize(Survey::class);
        $user = $this->prophesize(User::class);

        $entity = $this->responseFactory->createEntity($survey->reveal(), $user->reveal());

        $this->assertEquals($survey->reveal(), $entity->getSurvey());
        $this->assertEquals($user->reveal(), $entity->getUser());
        $this->assertEmpty($entity->getAnswers());
    }
}
