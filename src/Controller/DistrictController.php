<?php

namespace App\Controller;

use App\Service\DistrictService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DistrictController extends AbstractController
{
    private DistrictService $districtService;

    public function __construct(
        DistrictService $districtService
    ) {
        $this->districtService = $districtService;
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
        $localeSlug = $this->districtService->getLocaleSlugByDistrictSlug($districtSlug);

        return $this->redirect('/l/'.$localeSlug, Response::HTTP_MOVED_PERMANENTLY);
    }
}
