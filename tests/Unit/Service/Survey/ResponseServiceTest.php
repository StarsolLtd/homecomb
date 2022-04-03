<?php

namespace App\Tests\Unit\Service\Survey;

use App\Entity\Survey\Response;
use App\Entity\Survey\Survey;
use App\Entity\User;
use App\Factory\Survey\ResponseFactory;
use App\Service\Survey\ResponseService;
use App\Service\User\UserService;
use App\Tests\Unit\EntityManagerTrait;
use App\Tests\Unit\UserEntityFromInterfaceTrait;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

final class ResponseServiceTest extends TestCase
{
    use EntityManagerTrait;
    use ProphecyTrait;
    use UserEntityFromInterfaceTrait;

    private ResponseService $responseService;

    private ObjectProphecy $sessionService;
    private ObjectProphecy $responseFactory;

    public function setUp(): void
    {
        $this->entityManager = $this->prophesize(EntityManagerInterface::class);
        $this->userService = $this->prophesize(UserService::class);
        $this->responseFactory = $this->prophesize(ResponseFactory::class);

        $this->responseService = new ResponseService(
            $this->entityManager->reveal(),
            $this->userService->reveal(),
            $this->responseFactory->reveal(),
        );
    }

    public function testCreate1(): void
    {
        $response = $this->prophesize(Response::class);
        $survey = $this->prophesize(Survey::class);
        $user = $this->prophesize(User::class);

        $this->assertGetUserEntityOrNullFromInterface($user);

        $this->responseFactory->createEntity($survey, $user)->shouldBeCalledOnce()->willReturn($response);

        $this->assertEntitiesArePersistedAndFlush([$response]);

        $output = $this->responseService->create($survey->reveal(), $user->reveal());

        $this->assertEquals($response->reveal(), $output);
    }
}
