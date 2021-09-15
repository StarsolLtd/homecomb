<?php

namespace App\Tests\Unit\Service;

use App\Entity\Survey\Answer;
use App\Entity\Survey\Question;
use App\Entity\Survey\Response;
use App\Entity\Survey\Survey;
use App\Entity\User;
use App\Factory\Survey\AnswerFactory;
use App\Factory\Survey\SurveyFactory;
use App\Model\Interaction\RequestDetails;
use App\Model\Survey\SubmitAnswerInput;
use App\Model\Survey\View;
use App\Repository\Survey\AnswerRepository;
use App\Repository\Survey\QuestionRepository;
use App\Repository\Survey\ResponseRepository;
use App\Repository\Survey\SurveyRepository;
use App\Service\InteractionService;
use App\Service\ResponseService;
use App\Service\SessionService;
use App\Service\SurveyService;
use App\Tests\Unit\EntityManagerTrait;
use App\Tests\Unit\UserEntityFromInterfaceTrait;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @covers \App\Service\SurveyService
 */
class SurveyServiceTest extends TestCase
{
    use EntityManagerTrait;
    use ProphecyTrait;
    use UserEntityFromInterfaceTrait;

    private SurveyService $surveyService;

    private ObjectProphecy $interactionService;
    private ObjectProphecy $responseService;
    private ObjectProphecy $sessionService;
    private ObjectProphecy $answerFactory;
    private ObjectProphecy $surveyFactory;
    private ObjectProphecy $answerRepository;
    private ObjectProphecy $questionRepository;
    private ObjectProphecy $responseRepository;
    private ObjectProphecy $surveyRepository;

    public function setUp(): void
    {
        $this->entityManager = $this->prophesize(EntityManagerInterface::class);
        $this->interactionService = $this->prophesize(InteractionService::class);
        $this->responseService = $this->prophesize(ResponseService::class);
        $this->sessionService = $this->prophesize(SessionService::class);
        $this->answerFactory = $this->prophesize(AnswerFactory::class);
        $this->surveyFactory = $this->prophesize(SurveyFactory::class);
        $this->answerRepository = $this->prophesize(AnswerRepository::class);
        $this->questionRepository = $this->prophesize(QuestionRepository::class);
        $this->responseRepository = $this->prophesize(ResponseRepository::class);
        $this->surveyRepository = $this->prophesize(SurveyRepository::class);

        $this->surveyService = new SurveyService(
            $this->entityManager->reveal(),
            $this->interactionService->reveal(),
            $this->responseService->reveal(),
            $this->sessionService->reveal(),
            $this->answerFactory->reveal(),
            $this->surveyFactory->reveal(),
            $this->answerRepository->reveal(),
            $this->questionRepository->reveal(),
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
        $existingAnswer = $this->prophesize(Answer::class);
        $answer = $this->prophesize(Answer::class);
        $question = $this->prophesize(Question::class);
        $requestDetails = $this->prophesize(RequestDetails::class);
        $response = $this->prophesize(Response::class);
        $survey = $this->prophesize(Survey::class);
        $user = $this->prophesize(User::class);

        $input->getQuestionId()->shouldBeCalledOnce()->willReturn(33);

        $this->questionRepository->findOnePublishedById(33)->shouldBeCalledOnce()->willReturn($question);

        $question->getSurvey()->shouldBeCalledOnce()->willReturn($survey);

        $survey->getId()->shouldBeCalledOnce()->willReturn(22);

        $this->sessionService->get('survey_22_response_id')->willReturn(44);

        $this->responseRepository->findOneById(44)
            ->shouldBeCalledOnce()
            ->willReturn($response);

        $this->answerFactory->createEntityFromSubmitInput($input, $response)
            ->shouldBeCalledOnce()
            ->willReturn($answer);

        $this->answerRepository->findByQuestionAndResponse($question, $response)
            ->shouldBeCalledOnce()
            ->willReturn([$existingAnswer]);

        $this->entityManager->remove($existingAnswer)->shouldBeCalledOnce();

        $this->assertEntitiesArePersistedAndFlush([$answer]);

        $answer->getId()->shouldBeCalledOnce()->willReturn(234);

        $this->interactionService->record('Answer', 234, $requestDetails, $user)->shouldBeCalledOnce();

        $output = $this->surveyService->answer($input->reveal(), $user->reveal(), $requestDetails->reveal());

        $this->assertTrue($output->isSuccess());
    }
}
