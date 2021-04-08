<?php

namespace App\Command;

use App\Exception\NotFoundException;
use App\Repository\ReviewSolicitationRepository;
use App\Service\ReviewSolicitationService;
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

    private ReviewSolicitationRepository $reviewSolicitationRepository;
    private ReviewSolicitationService $reviewSolicitationService;

    public function __construct(
        ReviewSolicitationRepository $reviewSolicitationRepository,
        ReviewSolicitationService $reviewSolicitationService
    ) {
        $this->reviewSolicitationRepository = $reviewSolicitationRepository;
        $this->reviewSolicitationService = $reviewSolicitationService;

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
        $reviewSolicitationId = $input->getArgument('arg1');

        $arg1Type = gettype($reviewSolicitationId);
        if ('string' === $arg1Type) {
            /** @phpstan-ignore-next-line */
            $reviewSolicitationId = (int) $reviewSolicitationId;
        } elseif ('int' !== $arg1Type) {
            throw new \RuntimeException('Invalid type of arg1: '.$arg1Type);
        }

        $io->note(sprintf('Sending email for review solicitation %d', $reviewSolicitationId));

        $reviewSolicitation = $this->reviewSolicitationRepository->find($reviewSolicitationId);

        if (!$reviewSolicitation) {
            throw new NotFoundException(sprintf('ReviewSolicitation %d not found.', $reviewSolicitationId));
        }

        $this->reviewSolicitationService->send($reviewSolicitation);

        return Command::SUCCESS;
    }
}
