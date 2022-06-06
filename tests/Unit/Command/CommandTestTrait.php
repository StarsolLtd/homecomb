<?php

namespace App\Tests\Unit\Command;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

trait CommandTestTrait
{
    private CommandTester $commandTester;

    private function setupCommandTester(string $commandName): void
    {
        $application = new Application();
        $application->add($this->command);
        $command = $application->find($commandName);
        $this->commandTester = new CommandTester($command);
    }
}
