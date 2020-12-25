<?php

namespace App\Controller\Api;

use App\Controller\AppController;
use App\Service\BranchService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class BranchController extends AppController
{
    private BranchService $branchService;
    private SerializerInterface $serializer;

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
    public function view(string $slug, Request $request): JsonResponse
    {
        $view = $this->branchService->getViewBySlug($slug);

        return new JsonResponse(
            $this->serializer->serialize($view, 'json'),
            Response::HTTP_OK,
            [],
            true
        );
    }
}
