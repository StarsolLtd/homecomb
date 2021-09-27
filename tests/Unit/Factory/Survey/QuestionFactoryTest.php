<?php

namespace App\Tests\Unit\Factory\Survey;

use App\Entity\Survey\Choice;
use App\Entity\Survey\Question;
use App\Factory\Survey\ChoiceFactory;
use App\Factory\Survey\QuestionFactory;
use App\Model\Survey\Choice as ChoiceModel;
use App\Tests\Unit\SetIdByReflectionTrait;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @covers \App\Factory\Survey\QuestionFactory
 */
final class QuestionFactoryTest extends TestCase
{
    use ProphecyTrait;
    use SetIdByReflectionTrait;

    private QuestionFactory $questionFactory;

    private ObjectProphecy $choiceFactory;

    public function setUp(): void
    {
        $this->choiceFactory = $this->prophesize(ChoiceFactory::class);

        $this->questionFactory = new QuestionFactory(
            $this->choiceFactory->reveal()
        );
    }

    /**
     * @covers \App\Factory\Survey\QuestionFactory::createModelFromEntity
     */
    public function testCreateViewFromEntity1(): void
    {
        $question = (new Question())
            ->setType('open')
            ->setContent('How full is your glass?')
            ->setHelp('Halfway what?')
            ->setHighMeaning('Half-full')
            ->setLowMeaning('Half-empty')
            ->setSortOrder(43)
        ;
        $this->setIdByReflection($question, 125);

        $choice1 = $this->prophesize(Choice::class);
        $choice1Model = $this->prophesize(ChoiceModel::class);
        $choice1->isPublished()->shouldBeCalledOnce()->willReturn(true);
        $choice1->getSortOrder()->shouldBeCalledOnce()->willReturn(100);
        $choice1->setSortOrder(1)->shouldBeCalledOnce()->willReturn($choice1);
        $choice1->setQuestion($question)->shouldBeCalledOnce()->willReturn($choice1);
        $question->addChoice($choice1->reveal());

        $choice2 = $this->prophesize(Choice::class);
        $choice2Model = $this->prophesize(ChoiceModel::class);
        $choice2->getSortOrder()->shouldBeCalledOnce()->willReturn(100);
        $choice2->setSortOrder(2)->shouldBeCalledOnce()->willReturn($choice2);
        $choice2->isPublished()->shouldBeCalledOnce()->willReturn(true);
        $choice2->setQuestion($question)->shouldBeCalledOnce()->willReturn($choice2);
        $question->addChoice($choice2->reveal());

        $this->choiceFactory->createModelFromEntity($choice1)->shouldBeCalledOnce()->willReturn($choice1Model);
        $this->choiceFactory->createModelFromEntity($choice2)->shouldBeCalledOnce()->willReturn($choice2Model);

        $model = $this->questionFactory->createModelFromEntity($question);

        $this->assertEquals(125, $model->getId());
        $this->assertEquals('open', $model->getType());
        $this->assertEquals('How full is your glass?', $model->getContent());
        $this->assertEquals('Halfway what?', $model->getHelp());
        $this->assertEquals('Half-full', $model->getHighMeaning());
        $this->assertEquals('Half-empty', $model->getLowMeaning());
        $this->assertEquals(43, $model->getSortOrder());
        $this->assertCount(2, $model->getChoices());
    }
}
