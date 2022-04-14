<?php

namespace App\Command;

use App\Exception\NotFoundException;
use App\Repository\TenancyReviewRepositoryInterface;
use App\Service\TenancyReview\GenerateLocalesService;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class ReviewGenerateLocalesCommand extends Command
{
    protected static $defaultName = 'review:generate-locales';

    public function __construct(
        private TenancyReviewRepositoryInterface $tenancyReviewRepository,
        private GenerateLocalesService $generateLocalesService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Generate locales for a review')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Review ID')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $tenancyReviewId = $input->getArgument('arg1');

        $arg1Type = gettype($tenancyReviewId);
        if ('string' === $arg1Type) {
            $tenancyReviewId = (int) $tenancyReviewId;
        } elseif ('int' !== $arg1Type) {
            throw new RuntimeException('Invalid type of arg1: '.$arg1Type);
        }

        $io->note(sprintf('Generating locals for review %s', $tenancyReviewId));

        $tenancyReview = $this->tenancyReviewRepository->find($tenancyReviewId);

        if (!$tenancyReview) {
            throw new NotFoundException(sprintf('TenancyReview %s not found.', $tenancyReviewId));
        }

        $locales = $this->generateLocalesService->generateLocales($tenancyReview);

        foreach ($locales as $locale) {
            $io->note(sprintf('Locale %s associated with review.', $locale->getName()));
        }

        return Command::SUCCESS;
    }
}
