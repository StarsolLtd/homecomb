<?php

namespace App\Tests\Unit\Service;

use App\Entity\Survey\Response;
use App\Entity\Survey\Survey;
use App\Entity\User;
use App\Factory\Survey\ResponseFactory;
use App\Model\Survey\CreateResponseInput;
use App\Service\ResponseService;
use App\Service\SessionService;
use App\Service\UserService;
use App\Tests\Unit\EntityManagerTrait;
use App\Tests\Unit\UserEntityFromInterfaceTrait;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @covers \App\Service\ResponseService
 */
class ResponseServiceTest extends TestCase
{
    use EntityManagerTrait;
    use ProphecyTrait;
    use UserEntityFromInterfaceTrait;

    private ResponseService $responseService;

    private $sessionService;
    private $responseFactory;

    public function setUp(): void
    {
        $this->entityManager = $this->prophesize(EntityManagerInterface::class);
        $this->sessionService = $this->prophesize(SessionService::class);
        $this->userService = $this->prophesize(UserService::class);
        $this->responseFactory = $this->prophesize(ResponseFactory::class);

        $this->responseService = new ResponseService(
            $this->entityManager->reveal(),
            $this->sessionService->reveal(),
            $this->userService->reveal(),
            $this->responseFactory->reveal(),
        );
    }

    /**
     * @covers \App\Service\ResponseService::create
     */
    public function testCreate1(): void
    {
        $input = $this->prophesize(CreateResponseInput::class);
        $response = $this->prophesize(Response::class);
        $survey = $this->prophesize(Survey::class);
        $user = $this->prophesize(User::class);

        $this->assertGetUserEntityOrNullFromInterface($user);

        $this->responseFactory->createEntityFromCreateInput($input, $user)->shouldBeCalledOnce()->willReturn($response);

        $this->assertEntitiesArePersistedAndFlush([$response]);

        $response->getSurvey()->shouldBeCalledOnce()->willReturn($survey);

        $survey->getId()->shouldBeCalledOnce()->willReturn(55);

        $response->getId()->shouldBeCalledOnce()->willReturn(77);

        $this->sessionService->set('survey_55_response_id', 77)->shouldBeCalledOnce();

        $output = $this->responseService->create($input->reveal(), $user->reveal());

        $this->assertTrue($output->isSuccess());
    }
}
