<?php

namespace App\Controller\Api;

use App\Controller\AppController;
use App\Exception\NotFoundException;
use App\Service\BranchService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class BranchController extends AppController
{
    private BranchService $branchService;

    public function __construct(
        BranchService $branchService,
        SerializerInterface $serializer
    ) {
        $this->branchService = $branchService;
        $this->serializer = $serializer;
    }

    /**
     * @Route (
     *     "/api/branch/{slug}",
     *     name="api-branch-view",
     *     methods={"GET"}
     * )
     */
    public function view(string $slug): JsonResponse
    {
        try {
            $view = $this->branchService->getViewBySlug($slug);
        } catch (NotFoundException $e) {
            return $this->jsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        return $this->jsonResponse($view, Response::HTTP_OK);
    }
}
