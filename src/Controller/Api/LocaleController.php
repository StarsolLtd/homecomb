<?php

namespace App\Controller\Api;

use App\Controller\AppController;
use App\Exception\NotFoundException;
use App\Service\LocaleService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class LocaleController extends AppController
{
    private LocaleService $localeService;

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
        try {
            $view = $this->localeService->getViewBySlug($slug);
        } catch (NotFoundException $e) {
            return $this->jsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        return $this->jsonResponse($view, Response::HTTP_OK);
    }

    /**
     * @Route (
     *     "/api/locale-search",
     *     name="api-locale-search",
     *     methods={"GET"}
     * )
     */
    public function search(Request $request): JsonResponse
    {
        $output = $this->localeService->search((string) $request->query->get('q'));

        return $this->jsonResponse($output, Response::HTTP_OK);
    }
}
