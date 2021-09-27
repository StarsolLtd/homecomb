<?php

namespace App\Tests\Unit\Factory\Survey;

use App\Entity\Survey\Answer;
use App\Entity\Survey\Choice;
use App\Entity\Survey\Question;
use App\Entity\Survey\Response;
use App\Factory\Survey\AnswerFactory;
use App\Model\Survey\SubmitAnswerInput;
use App\Repository\Survey\ChoiceRepository;
use App\Repository\Survey\QuestionRepository;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @covers \App\Factory\Survey\AnswerFactory
 */
final class AnswerFactoryTest extends TestCase
{
    use ProphecyTrait;

    private AnswerFactory $answerFactory;

    private ObjectProphecy $choiceRepository;
    private ObjectProphecy $questionRepository;

    public function setUp(): void
    {
        $this->choiceRepository = $this->prophesize(ChoiceRepository::class);
        $this->questionRepository = $this->prophesize(QuestionRepository::class);

        $this->answerFactory = new AnswerFactory(
            $this->choiceRepository->reveal(),
            $this->questionRepository->reveal()
        );
    }

    /**
     * @covers \App\Factory\Survey\AnswerFactory::createEntityFromSubmitInput
     */
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

        $input = new SubmitAnswerInput(
            55,
            'It is yummy',
            75,
            3
        );

        $response = $this->prophesize(Response::class);

        $entity = $this->answerFactory->createEntityFromSubmitInput($input, $response->reveal());

        $this->assertEquals($question->reveal(), $entity->getQuestion());
        $this->assertEquals($response->reveal(), $entity->getResponse());
        $this->assertEquals('It is yummy', $entity->getContent());
        $this->assertEquals(3, $entity->getRating());
        $this->assertCount(1, $entity->getChoices());
        $this->assertEquals($choice->reveal(), $entity->getChoices()->first());
    }
}
