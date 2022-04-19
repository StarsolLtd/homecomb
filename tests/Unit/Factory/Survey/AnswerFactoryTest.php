<?php

namespace App\Tests\Unit\Factory\Survey;

use App\Entity\Survey\Answer;
use App\Entity\Survey\Choice;
use App\Entity\Survey\Question;
use App\Entity\Survey\Response;
use App\Factory\Survey\AnswerFactory;
use App\Model\Survey\SubmitAnswerInputInterface;
use App\Repository\Survey\ChoiceRepositoryInterface;
use App\Repository\Survey\QuestionRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

final class AnswerFactoryTest extends TestCase
{
    use ProphecyTrait;

    private AnswerFactory $answerFactory;

    private ObjectProphecy $choiceRepository;
    private ObjectProphecy $questionRepository;

    public function setUp(): void
    {
        $this->choiceRepository = $this->prophesize(ChoiceRepositoryInterface::class);
        $this->questionRepository = $this->prophesize(QuestionRepositoryInterface::class);

        $this->answerFactory = new AnswerFactory(
            $this->choiceRepository->reveal(),
            $this->questionRepository->reveal(),
        );
    }

    public function testCreateEntityFromSubmitInput1(): void
    {
        $choice = $this->prophesize(Choice::class);
        $question = $this->prophesize(Question::class);

        $this->choiceRepository->findOnePublishedById(75)
            ->shouldBeCalledOnce()
            ->willReturn($choice);

        $this->questionRepository->findOnePublishedById(55)
            ->shouldBeCalledOnce()
            ->willReturn($question);

        $choice->addAnswer(Argument::type(Answer::class))
            ->shouldBeCalledOnce()
            ->willReturn($choice);

        $input = $this->prophesize(SubmitAnswerInputInterface::class);
        $input->getQuestionId()->shouldBeCalledOnce()->willReturn(55);
        $input->getContent()->shouldBeCalledOnce()->willReturn('It is yummy');
        $input->getChoiceId()->shouldBeCalledOnce()->willReturn(75);
        $input->getRating()->shouldBeCalledOnce()->willReturn(3);

        $response = $this->prophesize(Response::class);

        $entity = $this->answerFactory->createEntityFromSubmitInput($input->reveal(), $response->reveal());

        $this->assertEquals($question->reveal(), $entity->getQuestion());
        $this->assertEquals($response->reveal(), $entity->getResponse());
        $this->assertEquals('It is yummy', $entity->getContent());
        $this->assertEquals(3, $entity->getRating());
        $this->assertCount(1, $entity->getChoices());
        $this->assertEquals($choice->reveal(), $entity->getChoices()->first());
    }
}
