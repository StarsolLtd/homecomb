<?php

namespace App\Controller\Api;

use App\Controller\AppController;
use App\Service\LocaleService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class LocaleController extends AppController
{
    private LocaleService $localeService;
    private SerializerInterface $serializer;

    public function __construct(
        LocaleService $localeService,
        SerializerInterface $serializer
    ) {
        $this->localeService = $localeService;
        $this->serializer = $serializer;
    }

    /**
     * @Route (
     *     "/api/l/{slug}",
     *     name="api-locale-view",
     *     methods={"GET"}
     * )
     */
    public function view(string $slug, Request $request): JsonResponse
    {
        $view = $this->localeService->getViewBySlug($slug);

        return new JsonResponse(
            $this->serializer->serialize($view, 'json'),
            Response::HTTP_OK,
            [],
            true
        );
    }
}
