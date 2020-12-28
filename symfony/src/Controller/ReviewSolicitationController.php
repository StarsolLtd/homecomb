<?php

namespace App\Controller;

use App\Exception\NotFoundException;
use App\Repository\ReviewSolicitationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReviewSolicitationController extends AbstractController
{
    private ReviewSolicitationRepository $reviewSolicitationRepository;

    public function __construct(
        ReviewSolicitationRepository $reviewSolicitationRepository
    ) {
        $this->reviewSolicitationRepository = $reviewSolicitationRepository;
    }

    /**
     * @Route (
     *     "/rs/{code}",
     *     name="review-solicitation-respond",
     *     methods={"GET", "HEAD"}
     * )
     */
    public function viewByCode(string $code): Response
    {
        try {
            $this->reviewSolicitationRepository->findOneUnfinishedByCode($code);
        } catch (NotFoundException $e) {
            throw $this->createNotFoundException($e->getMessage());
        }

        return $this->render('index.html.twig');
    }
}
