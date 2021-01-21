<?php

namespace App\Tests\Unit\Service;

use App\Entity\Survey\Answer;
use App\Entity\Survey\Response;
use App\Entity\Survey\Survey;
use App\Factory\Survey\AnswerFactory;
use App\Factory\Survey\SurveyFactory;
use App\Model\Survey\SubmitAnswerInput;
use App\Model\Survey\View;
use App\Repository\Survey\ResponseRepository;
use App\Repository\Survey\SurveyRepository;
use App\Service\InteractionService;
use App\Service\SurveyService;
use App\Tests\Unit\EntityManagerTrait;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @covers \App\Service\SurveyService
 */
class SurveyServiceTest extends TestCase
{
    use EntityManagerTrait;
    use ProphecyTrait;

    private SurveyService $surveyService;

    private $entityManager;
    private $interactionService;
    private $answerFactory;
    private $surveyFactory;
    private $responseRepository;
    private $surveyRepository;

    public function setUp(): void
    {
        $this->entityManager = $this->prophesize(EntityManagerInterface::class);
        $this->interactionService = $this->prophesize(InteractionService::class);
        $this->answerFactory = $this->prophesize(AnswerFactory::class);
        $this->surveyFactory = $this->prophesize(SurveyFactory::class);
        $this->responseRepository = $this->prophesize(ResponseRepository::class);
        $this->surveyRepository = $this->prophesize(SurveyRepository::class);

        $this->surveyService = new SurveyService(
            $this->entityManager->reveal(),
            $this->interactionService->reveal(),
            $this->answerFactory->reveal(),
            $this->surveyFactory->reveal(),
            $this->responseRepository->reveal(),
            $this->surveyRepository->reveal(),
        );
    }

    /**
     * @covers \App\Service\SurveyService::getViewBySlug
     */
    public function testGetViewBySlug1(): void
    {
        $survey = $this->prophesize(Survey::class);
        $view = $this->prophesize(View::class);

        $this->surveyRepository->findOnePublishedBySlug('surveyslug')
            ->shouldBeCalledOnce()
            ->willReturn($survey);

        $this->surveyFactory->createViewFromEntity($survey)
            ->shouldBeCalledOnce()
            ->willReturn($view);

        $output = $this->surveyService->getViewBySlug('surveyslug');

        $this->assertEquals($view->reveal(), $output);
    }

    /**
     * @covers \App\Service\SurveyService::answer
     */
    public function testAnswer1(): void
    {
        $input = $this->prophesize(SubmitAnswerInput::class);
        $answer = $this->prophesize(Answer::class);
        $response = $this->prophesize(Response::class);

        $this->responseRepository->findOneById(44)
            ->shouldBeCalledOnce()
            ->willReturn($response);

        $this->answerFactory->createEntityFromSubmitInput($input, $response)
            ->shouldBeCalledOnce()
            ->willReturn($answer);

        $this->assertEntitiesArePersistedAndFlush([$answer]);

        $output = $this->surveyService->answer($input->reveal(), 44);

        $this->assertTrue($output->isSuccess());
    }
}
