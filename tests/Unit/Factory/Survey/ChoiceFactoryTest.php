<?php

namespace App\Tests\Unit\Factory\Survey;

use App\Entity\Survey\Choice;
use App\Factory\Survey\ChoiceFactory;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @covers \App\Factory\Survey\ChoiceFactory
 */
final class ChoiceFactoryTest extends TestCase
{
    use ProphecyTrait;

    private ChoiceFactory $choiceFactory;

    public function setUp(): void
    {
        $this->choiceFactory = new ChoiceFactory();
    }

    public function testCreateModeFromEntity1(): void
    {
        $choice = $this->prophesize(Choice::class);
        $choice->getId()->shouldBeCalledOnce()->willReturn(125);
        $choice->getName()->shouldBeCalledOnce()->willReturn('Hat');
        $choice->getHelp()->shouldBeCalledOnce()->willReturn('Like you wear on your head');
        $choice->getSortOrder()->shouldBeCalledOnce()->willReturn(43);

        $model = $this->choiceFactory->createModelFromEntity($choice->reveal());

        $this->assertEquals(125, $model->getId());
        $this->assertEquals('Hat', $model->getName());
        $this->assertEquals('Like you wear on your head', $model->getHelp());
        $this->assertEquals(43, $model->getSortOrder());
    }
}
