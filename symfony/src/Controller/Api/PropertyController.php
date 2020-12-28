<?php

namespace App\Controller\Api;

use App\Controller\AppController;
use App\Exception\NotFoundException;
use App\Model\SuggestPropertyInput;
use App\Service\GetAddressService;
use App\Service\PropertyService;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class PropertyController extends AppController
{
    private GetAddressService $getAddressService;
    private PropertyService $propertyService;
    private UserService $userService;

    public function __construct(
        GetAddressService $getAddressService,
        PropertyService $propertyService,
        UserService $userService,
        SerializerInterface $serializer
    ) {
        $this->getAddressService = $getAddressService;
        $this->propertyService = $propertyService;
        $this->userService = $userService;
        $this->serializer = $serializer;
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

        $propertySlug = $this->propertyService->determinePropertySlugFromVendorPropertyId($vendorPropertyId);

        return new JsonResponse(
            [
                'slug' => $propertySlug,
            ],
            Response::HTTP_OK
        );
    }

    /**
     * @Route (
     *     "/api/property/suggest-property",
     *     name="suggest-property",
     *     methods={"GET"}
     * )
     */
    public function suggestProperty(Request $request): JsonResponse
    {
        $term = $request->query->get('term');
        if (null === $term) {
            return new JsonResponse([], Response::HTTP_BAD_REQUEST);
        }

        $input = new SuggestPropertyInput($term);

        $suggestions = $this->getAddressService->autocomplete($input->getSearch());

        $output = [];
        foreach ($suggestions as $suggestion) {
            $output[] = [
                'value' => $suggestion->getAddress(),
                'id' => $suggestion->getVendorId(),
            ];
        }

        return new JsonResponse(
            $output,
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
