<?php

namespace App\Controller;

use App\Service\CityService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CityController extends AbstractController
{
    public function __construct(
        private CityService $cityService,
    ) {
    }

    /**
     * @Route (
     *     "/c/{citySlug}",
     *     name="city-view-by-slug",
     *     methods={"GET", "HEAD"}
     * )
     */
    public function viewBySlug(string $citySlug): Response
    {
        $localeSlug = $this->cityService->getLocaleSlugByCitySlug($citySlug);

        return $this->redirect('/l/'.$localeSlug, Response::HTTP_MOVED_PERMANENTLY);
    }
}
