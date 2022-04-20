<?php

namespace App\Tests\Unit\Factory\Survey;

use App\Entity\Survey\Choice;
use App\Entity\Survey\Question;
use App\Factory\Survey\ChoiceFactory;
use App\Factory\Survey\QuestionFactory;
use App\Model\Survey\Choice as ChoiceModel;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

final class QuestionFactoryTest extends TestCase
{
    use ProphecyTrait;

    private QuestionFactory $questionFactory;

    private ObjectProphecy $choiceFactory;

    public function setUp(): void
    {
        $this->choiceFactory = $this->prophesize(ChoiceFactory::class);

        $this->questionFactory = new QuestionFactory(
            $this->choiceFactory->reveal()
        );
    }

    public function testCreateModelFromEntity1(): void
    {
        $choice1 = $this->prophesize(Choice::class);
        $choice1Model = $this->prophesize(ChoiceModel::class);

        $choice2 = $this->prophesize(Choice::class);
        $choice2Model = $this->prophesize(ChoiceModel::class);

        $this->choiceFactory->createModelFromEntity($choice1)->shouldBeCalledOnce()->willReturn($choice1Model);
        $this->choiceFactory->createModelFromEntity($choice2)->shouldBeCalledOnce()->willReturn($choice2Model);

        $choicesCollection = (new ArrayCollection());
        $choicesCollection->add($choice1->reveal());
        $choicesCollection->add($choice2->reveal());

        $question = $this->prophesize(Question::class);
        $question->getPublishedChoices()->shouldBeCalledOnce()->willReturn($choicesCollection);
        $question->getId()->shouldBeCalledOnce()->willReturn(125);
        $question->getType()->shouldBeCalledOnce()->willReturn('open');
        $question->getContent()->shouldBeCalledOnce()->willReturn('How full is your glass?');
        $question->getHelp()->shouldBeCalledOnce()->willReturn('Halfway what?');
        $question->getHighMeaning()->shouldBeCalledOnce()->willReturn('Half-full');
        $question->getLowMeaning()->shouldBeCalledOnce()->willReturn('Half-empty');
        $question->getSortOrder()->shouldBeCalledOnce()->willReturn(43);

        $model = $this->questionFactory->createModelFromEntity($question->reveal());

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
