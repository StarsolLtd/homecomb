<?php

namespace App\Controller;

use App\Service\District\GetLocaleService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class DistrictController extends AbstractController
{
    public function __construct(
        private GetLocaleService $getLocaleService,
    ) {
    }

    /**
     * @Route (
     *     "/d/{districtSlug}",
     *     name="district-view-by-slug",
     *     methods={"GET", "HEAD"}
     * )
     */
    public function viewBySlug(string $districtSlug): Response
    {
        $localeSlug = $this->getLocaleService->getLocaleSlugByDistrictSlug($districtSlug);

        return $this->redirect('/l/'.$localeSlug, Response::HTTP_MOVED_PERMANENTLY);
    }
}
