<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class TenancyReviewSolicitationController extends AbstractController
{
    /**
     * @Route ("/rs/{code}", name="rs-code", methods={"GET", "HEAD"})
     * @Route ("/review-your-tenancy/{code}", name="review-your-tenancy-code", methods={"GET", "HEAD"})
     */
    public function viewByCode(string $code): Response
    {
        return $this->render('index.html.twig');
    }
}
