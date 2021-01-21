<?php

namespace App\Tests\Unit\Service;

use App\Entity\Survey\Survey;
use App\Factory\Survey\AnswerFactory;
use App\Factory\Survey\SurveyFactory;
use App\Model\Survey\View;
use App\Repository\Survey\ResponseRepository;
use App\Repository\Survey\SurveyRepository;
use App\Service\InteractionService;
use App\Service\SurveyService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class SurveyServiceTest extends TestCase
{
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

    public function testGetViewBySlug(): void
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
}
