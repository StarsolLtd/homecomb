<?php

namespace App\Tests\Unit\Factory\Survey;

use App\Entity\Survey\Question;
use App\Factory\Survey\QuestionFactory;
use App\Tests\Unit\SetIdByReflectionTrait;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @covers \App\Factory\Survey\QuestionFactory
 */
class QuestionFactoryTest extends TestCase
{
    use ProphecyTrait;
    use SetIdByReflectionTrait;

    private QuestionFactory $questionFactory;

    public function setUp(): void
    {
        $this->questionFactory = new QuestionFactory();
    }

    /**
     * @covers \App\Factory\Survey\QuestionFactory::createViewFromEntity
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

        $view = $this->questionFactory->createViewFromEntity($question);

        $this->assertEquals(125, $view->getId());
        $this->assertEquals('open', $view->getType());
        $this->assertEquals('How full is your glass?', $view->getContent());
        $this->assertEquals('Halfway what?', $view->getHelp());
        $this->assertEquals('Half-full', $view->getHighMeaning());
        $this->assertEquals('Half-empty', $view->getLowMeaning());
        $this->assertEquals(43, $view->getSortOrder());
    }
}
