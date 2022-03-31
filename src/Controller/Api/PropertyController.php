<?php

namespace App\Controller\Api;

use App\Controller\AppController;
use App\Exception\DeveloperException;
use App\Exception\FailureException;
use App\Exception\NotFoundException;
use App\Service\PropertyService;
use App\Service\User\UserService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

final class PropertyController extends AppController
{
    use VerifyCaptchaTrait;

    public function __construct(
        private PropertyService $propertyService,
        private UserService $userService,
        protected SerializerInterface $serializer,
    ) {
    }

    /**
     * @Route (
     *     "/api/property/lookup-slug-from-vendor-id",
     *     name="lookup-slug-from-vendor-id",
     *     methods={"GET"}
     * )
     */
    public function lookupSlugFromVendorId(Request $request): JsonResponse
    {
        $vendorPropertyId = $request->query->get('vendorPropertyId');
        if (null === $vendorPropertyId) {
            return new JsonResponse([], Response::HTTP_BAD_REQUEST);
        }

        try {
            $propertySlug = $this->propertyService->determinePropertySlugFromVendorPropertyId((string) $vendorPropertyId);
        } catch (DeveloperException|FailureException $e) {
            return $this->jsonResponse(null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(
            [
                'slug' => $propertySlug,
            ],
            Response::HTTP_OK
        );
    }

    /**
     * @Route (
     *     "/api/property/lookup-slug-from-address",
     *     name="lookup-slug-from-address",
     *     methods={"GET"}
     * )
     */
    public function lookupSlugFromAddress(Request $request): JsonResponse
    {
        $addressLine1 = $request->query->get('addressLine1');
        $postcode = $request->query->get('postcode');
        if (null === $addressLine1 || null === $postcode) {
            return new JsonResponse([], Response::HTTP_BAD_REQUEST);
        }

        $propertySlug = $this->propertyService->determinePropertySlugFromAddress(
            (string) $addressLine1,
            (string) $postcode
        );

        return new JsonResponse(
            [
                'slug' => $propertySlug,
            ],
            Response::HTTP_OK
        );
    }

    /**
     * @Route (
     *     "/api/property/{slug}",
     *     name="api-property-view",
     *     methods={"GET"}
     * )
     */
    public function view(string $slug): JsonResponse
    {
        try {
            $view = $this->propertyService->getViewBySlug($slug);
        } catch (NotFoundException $e) {
            return $this->jsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        return $this->jsonResponse($view, Response::HTTP_OK);
    }
}
