<?php

namespace App\Command;

use App\Exception\NotFoundException;
use App\Repository\ReviewRepository;
use App\Service\ReviewService;
use function gettype;
use function sprintf;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ReviewGenerateLocalesCommand extends Command
{
    protected static $defaultName = 'review:generate-locales';

    private ReviewRepository $reviewRepository;
    private ReviewService $reviewService;

    public function __construct(
        ReviewRepository $reviewRepository,
        ReviewService $reviewService
    ) {
        $this->reviewRepository = $reviewRepository;
        $this->reviewService = $reviewService;

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
        $reviewId = $input->getArgument('arg1');

        $arg1Type = gettype($reviewId);
        if ('string' === $arg1Type) {
            /** @phpstan-ignore-next-line */
            $reviewId = (int) $reviewId;
        } elseif ('int' !== $arg1Type) {
            throw new \RuntimeException('Invalid type of arg1: '.$arg1Type);
        }

        $io->note(sprintf('Generating locals for review %s', $reviewId));

        $review = $this->reviewRepository->find($reviewId);

        if (!$review) {
            throw new NotFoundException(sprintf('Review %s not found.', $reviewId));
        }

        $locales = $this->reviewService->generateLocales($review);

        foreach ($locales as $locale) {
            $io->note(sprintf('Locale %s associated with review.', $locale->getName()));
        }

        return Command::SUCCESS;
    }
}
