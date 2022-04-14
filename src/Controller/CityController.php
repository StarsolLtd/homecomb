<?php

namespace App\Controller;

use App\Service\City\GetLocaleService as CityGetLocaleService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class CityController extends AbstractController
{
    public function __construct(
        private CityGetLocaleService $cityGetLocaleService,
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
        $localeSlug = $this->cityGetLocaleService->getLocaleSlugByCitySlug($citySlug);

        return $this->redirect('/l/'.$localeSlug, Response::HTTP_MOVED_PERMANENTLY);
    }
}
