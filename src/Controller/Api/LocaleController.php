<?php

namespace App\Controller\Api;

use App\Controller\AppController;
use App\Exception\NotFoundException;
use App\Service\Locale\SearchService;
use App\Service\Locale\ViewService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

final class LocaleController extends AppController
{
    public function __construct(
        private SearchService $searchService,
        private ViewService $viewService,
        protected SerializerInterface $serializer,
    ) {
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
            $view = $this->viewService->getViewBySlug($slug);
        } catch (NotFoundException $e) {
            return $this->jsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        return $this->jsonResponse($view, Response::HTTP_OK);
    }

    /**
     * @Route (
     *     "/api/locale/suggest-locale",
     *     name="api-suggest-locale",
     *     methods={"GET"}
     * )
     */
    public function search(Request $request): JsonResponse
    {
        $output = $this->searchService->search((string) $request->query->get('q'));

        return $this->jsonResponse($output, Response::HTTP_OK);
    }
}
