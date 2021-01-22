<?php

namespace App\Tests\Unit\Factory\Survey;

use App\Entity\Survey\Question;
use App\Entity\Survey\Response;
use App\Factory\Survey\AnswerFactory;
use App\Model\Survey\SubmitAnswerInput;
use App\Repository\Survey\QuestionRepository;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @covers \App\Factory\Survey\AnswerFactory
 */
class AnswerFactoryTest extends TestCase
{
    use ProphecyTrait;

    private AnswerFactory $answerFactory;

    private $questionRepository;

    public function setUp(): void
    {
        $this->questionRepository = $this->prophesize(QuestionRepository::class);

        $this->answerFactory = new AnswerFactory(
            $this->questionRepository->reveal()
        );
    }

    /**
     * @covers \App\Factory\Survey\AnswerFactory::createEntityFromSubmitInput
     */
    public function testCreateEntityFromSubmitInput1(): void
    {
        $question = $this->prophesize(Question::class);

        $this->questionRepository->findOnePublishedById(55)
            ->shouldBeCalledOnce()
            ->willReturn($question);

        $input = new SubmitAnswerInput(
            55,
            'It is yummy',
            3
        );

        $response = $this->prophesize(Response::class);

        $entity = $this->answerFactory->createEntityFromSubmitInput($input, $response->reveal());

        $this->assertEquals($question->reveal(), $entity->getQuestion());
        $this->assertEquals($response->reveal(), $entity->getResponse());
        $this->assertEquals('It is yummy', $entity->getContent());
        $this->assertEquals(3, $entity->getRating());
    }
}
