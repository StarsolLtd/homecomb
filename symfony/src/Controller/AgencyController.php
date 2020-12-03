<?php

namespace App\Controller;

use App\Exception\NotFoundException;
use App\Repository\AgencyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AgencyController extends AbstractController
{
    private AgencyRepository $agencyRepository;

    public function __construct(
        AgencyRepository $agencyRepository
    ) {
        $this->agencyRepository = $agencyRepository;
    }

    /**
     * @Route (
     *     "/agency/{slug}",
     *     name="agency-view-by-slug",
     *     methods={"GET", "HEAD"}
     * )
     */
    public function viewBySlug(string $slug): Response
    {
        try {
            $agency = $this->agencyRepository->findOnePublishedBySlug($slug);
        } catch (NotFoundException $e) {
            throw $this->createNotFoundException($e->getMessage());
        }

        return $this->render(
            'agency/view.html.twig',
            [
                'agency' => $agency,
            ]
        );
    }
}
