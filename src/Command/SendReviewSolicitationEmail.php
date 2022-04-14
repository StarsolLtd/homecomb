<?php

namespace App\Command;

use App\Exception\NotFoundException;
use App\Repository\TenancyReviewSolicitationRepositoryInterface;
use App\Service\TenancyReviewSolicitation\SendService;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SendReviewSolicitationEmail extends Command
{
    protected static $defaultName = 'email:review-solicitation';

    public function __construct(
        private TenancyReviewSolicitationRepositoryInterface $tenancyReviewSolicitationRepository,
        private SendService $sendService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Send a review solicitation email')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Review Solicitation ID')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $tenancyReviewSolicitationId = $input->getArgument('arg1');

        $arg1Type = gettype($tenancyReviewSolicitationId);
        if ('string' === $arg1Type) {
            $tenancyReviewSolicitationId = (int) $tenancyReviewSolicitationId;
        } elseif ('int' !== $arg1Type) {
            throw new RuntimeException('Invalid type of arg1: '.$arg1Type);
        }

        $io->note(sprintf('Sending email for review solicitation %d', $tenancyReviewSolicitationId));

        $tenancyReviewSolicitation = $this->tenancyReviewSolicitationRepository->find($tenancyReviewSolicitationId);

        if (!$tenancyReviewSolicitation) {
            throw new NotFoundException(sprintf('TenancyReviewSolicitation %d not found.', $tenancyReviewSolicitationId));
        }

        $this->sendService->send($tenancyReviewSolicitation);

        return Command::SUCCESS;
    }
}
