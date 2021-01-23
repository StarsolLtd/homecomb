<?php

namespace App\Tests\Unit\Factory\Survey;

use App\Entity\Survey\Choice;
use App\Factory\Survey\ChoiceFactory;
use App\Tests\Unit\SetIdByReflectionTrait;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @covers \App\Factory\Survey\ChoiceFactory
 */
class ChoiceFactoryTest extends TestCase
{
    use ProphecyTrait;
    use SetIdByReflectionTrait;

    private ChoiceFactory $choiceFactory;

    public function setUp(): void
    {
        $this->choiceFactory = new ChoiceFactory();
    }

    /**
     * @covers \App\Factory\Survey\ChoiceFactory::createModelFromEntity
     */
    public function testCreateModeFromEntity1(): void
    {
        $choice = (new Choice())
            ->setName('Hat')
            ->setHelp('Like you wear on your head')
            ->setSortOrder(43)
        ;
        $this->setIdByReflection($choice, 125);

        $model = $this->choiceFactory->createModelFromEntity($choice);

        $this->assertEquals(125, $model->getId());
        $this->assertEquals('Hat', $model->getName());
        $this->assertEquals('Like you wear on your head', $model->getHelp());
        $this->assertEquals(43, $model->getSortOrder());
    }
}
