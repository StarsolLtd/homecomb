<?php

namespace App\Command;

use App\Exception\NotFoundException;
use App\Repository\TenancyReviewSolicitationRepository;
use App\Service\TenancyReviewSolicitationService;
use function gettype;
use function sprintf;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SendReviewSolicitationEmail extends Command
{
    protected static $defaultName = 'email:review-solicitation';

    private TenancyReviewSolicitationRepository $tenancyReviewSolicitationRepository;
    private TenancyReviewSolicitationService $tenancyReviewSolicitationService;

    public function __construct(
        TenancyReviewSolicitationRepository $tenancyReviewSolicitationRepository,
        TenancyReviewSolicitationService $tenancyReviewSolicitationService
    ) {
        $this->tenancyReviewSolicitationRepository = $tenancyReviewSolicitationRepository;
        $this->tenancyReviewSolicitationService = $tenancyReviewSolicitationService;

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
            /** @phpstan-ignore-next-line */
            $tenancyReviewSolicitationId = (int) $tenancyReviewSolicitationId;
        } elseif ('int' !== $arg1Type) {
            throw new \RuntimeException('Invalid type of arg1: '.$arg1Type);
        }

        $io->note(sprintf('Sending email for review solicitation %d', $tenancyReviewSolicitationId));

        $tenancyReviewSolicitation = $this->tenancyReviewSolicitationRepository->find($tenancyReviewSolicitationId);

        if (!$tenancyReviewSolicitation) {
            throw new NotFoundException(sprintf('TenancyReviewSolicitation %d not found.', $tenancyReviewSolicitationId));
        }

        $this->tenancyReviewSolicitationService->send($tenancyReviewSolicitation);

        return Command::SUCCESS;
    }
}
