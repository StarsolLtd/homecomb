<?php

namespace App\Controller;

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
     * @Route ("/rs/{code}", name="rs-code", methods={"GET", "HEAD"})
     * @Route ("/review-your-tenancy/{code}", name="review-your-tenancy-code", methods={"GET", "HEAD"})
     */
    public function viewByCode(string $code): Response
    {
        return $this->render('index.html.twig');
    }
}
