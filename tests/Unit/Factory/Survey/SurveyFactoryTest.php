<?php

namespace App\Tests\Unit\Factory\Survey;

use App\Entity\Survey\Question;
use App\Entity\Survey\Survey;
use App\Factory\Survey\QuestionFactory;
use App\Factory\Survey\SurveyFactory;
use App\Model\Survey\Question as QuestionModel;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @covers \App\Factory\Survey\SurveyFactory
 */
final class SurveyFactoryTest extends TestCase
{
    use ProphecyTrait;

    private SurveyFactory $surveyFactory;

    private ObjectProphecy $questionFactory;

    public function setUp(): void
    {
        $this->questionFactory = $this->prophesize(QuestionFactory::class);

        $this->surveyFactory = new SurveyFactory(
            $this->questionFactory->reveal()
        );
    }

    /**
     * @covers \App\Factory\Survey\SurveyFactory::createViewFromEntity
     */
    public function testCreateViewFromEntity1(): void
    {
        $survey = (new Survey())
            ->setTitle('Chocolate bars of the United Kingdom')
            ->setDescription('Your thoughts on their impact on society and your own life.')
            ->setSlug('testsurveyslug')
        ;

        $question1 = $this->prophesize(Question::class);
        $question1->isPublished()->shouldBeCalledOnce()->willReturn(true);
        $question1->setSurvey($survey)->shouldBeCalledOnce();
        $question1Model = $this->prophesize(QuestionModel::class);

        $question2 = $this->prophesize(Question::class);
        $question2->isPublished()->shouldBeCalledOnce()->willReturn(true);
        $question2->setSurvey($survey)->shouldBeCalledOnce();
        $question2Model = $this->prophesize(QuestionModel::class);

        $survey
            ->addQuestion($question1->reveal())
            ->addQuestion($question2->reveal());

        $this->questionFactory->createModelFromEntity($question1)->shouldBeCalledOnce()->willReturn($question1Model);
        $this->questionFactory->createModelFromEntity($question2)->shouldBeCalledOnce()->willReturn($question2Model);

        $view = $this->surveyFactory->createViewFromEntity($survey);

        $this->assertEquals('Chocolate bars of the United Kingdom', $view->getTitle());
        $this->assertEquals('Your thoughts on their impact on society and your own life.', $view->getDescription());
        $this->assertEquals('testsurveyslug', $view->getSlug());
        $this->assertCount(2, $view->getQuestions());
    }
}
