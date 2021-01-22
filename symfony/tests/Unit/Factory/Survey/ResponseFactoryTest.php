<?php

namespace App\Tests\Unit\Factory\Survey;

use App\Entity\Survey\Survey;
use App\Entity\User;
use App\Factory\Survey\ResponseFactory;
use App\Model\Survey\CreateResponseInput;
use App\Repository\Survey\SurveyRepository;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @covers \App\Factory\Survey\ResponseFactory
 */
class ResponseFactoryTest extends TestCase
{
    use ProphecyTrait;

    private ResponseFactory $responseFactory;

    private $surveyRepository;

    public function setUp(): void
    {
        $this->surveyRepository = $this->prophesize(SurveyRepository::class);

        $this->responseFactory = new ResponseFactory(
            $this->surveyRepository->reveal()
        );
    }

    /**
     * @covers \App\Factory\Survey\ResponseFactory::createEntityFromCreateInput
     */
    public function testCreateEntityFromCreateInput1(): void
    {
        $survey = $this->prophesize(Survey::class);
        $user = $this->prophesize(User::class);

        $this->surveyRepository->findOnePublishedBySlug('testsurveyslug')
            ->shouldBeCalledOnce()
            ->willReturn($survey);

        $input = new CreateResponseInput('testsurveyslug');

        $entity = $this->responseFactory->createEntityFromCreateInput($input, $user->reveal());

        $this->assertEquals($survey->reveal(), $entity->getSurvey());
        $this->assertEquals($user->reveal(), $entity->getUser());
        $this->assertEmpty($entity->getAnswers());
    }
}
