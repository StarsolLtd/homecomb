<?php

namespace App\Controller\Api;

use App\Controller\AppController;
use App\Model\SuggestPropertyInput;
use App\Service\PropertyService;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class PropertyController extends AppController
{
    private PropertyService $propertyService;
    private UserService $userService;
    private SerializerInterface $serializer;

    public function __construct(
        PropertyService $propertyService,
        UserService $userService,
        SerializerInterface $serializer
    ) {
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

        $suggestions = $this->propertyService->suggestProperty($input);

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
}
