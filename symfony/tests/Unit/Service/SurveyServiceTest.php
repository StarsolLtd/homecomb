<?php

namespace App\Tests\Unit\Service;

use App\Entity\Survey\Survey;
use App\Factory\Survey\SurveyFactory;
use App\Model\Survey\View;
use App\Repository\Survey\SurveyRepository;
use App\Service\SurveyService;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class SurveyServiceTest extends TestCase
{
    use ProphecyTrait;

    private SurveyService $surveyService;

    private $surveyFactory;
    private $surveyRepository;

    public function setUp(): void
    {
        $this->surveyFactory = $this->prophesize(SurveyFactory::class);
        $this->surveyRepository = $this->prophesize(SurveyRepository::class);

        $this->surveyService = new SurveyService(
            $this->surveyFactory->reveal(),
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
