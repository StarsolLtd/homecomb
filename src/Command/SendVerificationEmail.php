<?php

namespace App\Command;

use App\Exception\NotFoundException;
use App\Repository\UserRepository;
use App\Service\User\RegistrationService;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SendVerificationEmail extends Command
{
    protected static $defaultName = 'email:verification';

    public function __construct(
        private UserRepository $userRepository,
        private RegistrationService $userRegistrationService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Send a verification email')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'User ID')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $userId = $input->getArgument('arg1');

        $arg1Type = gettype($userId);
        if ('string' === $arg1Type) {
            $userId = (int) $userId;
        } elseif ('int' !== $arg1Type) {
            throw new RuntimeException('Invalid type of arg1: '.$arg1Type);
        }

        $io->note(sprintf('Sending verification email for user %d', $userId));

        $user = $this->userRepository->find($userId);

        if (!$user) {
            throw new NotFoundException(sprintf('User %d not found.', $userId));
        }

        $sent = $this->userRegistrationService->sendVerificationEmail($user);

        if ($sent) {
            $io->success(sprintf('Email sent.'));

            return Command::SUCCESS;
        }

        $io->warning(sprintf('Email not sent.'));

        return Command::FAILURE;
    }
}
