<?php

namespace App\Controller;

use App\Model\LookupPropertyIdInput;
use App\Model\SuggestPropertyInput;
use App\Service\PropertyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class PropertyController extends AbstractController
{
    private PropertyService $propertyService;
    private SerializerInterface $serializer;

    public function __construct(
        PropertyService $propertyService,
        SerializerInterface $serializer
    ) {
        $this->propertyService = $propertyService;
        $this->serializer = $serializer;
    }

    /**
     * @Route (
     *     "/api/property/lookup-id",
     *     name="lookup-property-id",
     *     methods={"POST"}
     * )
     */
    public function lookupPropertyId(Request $request): JsonResponse
    {
        /** @var LookupPropertyIdInput $input */
        $input = $this->serializer->deserialize($request->getContent(), LookupPropertyIdInput::class, 'json');

        $output = $this->propertyService->lookupPropertyId($input);

        return JsonResponse::create(
            [
                'id' => $output->getId(),
            ],
            Response::HTTP_OK
        );
    }

    /**
     * @Route (
     *     "/api/property/lookup-id-from-vendor-id",
     *     name="lookup-id-from-vendor-id",
     *     methods={"GET"}
     * )
     */
    public function lookupPropertyIdFromVendorId(Request $request): JsonResponse
    {
        $propertyId = $this->propertyService->determinePropertyIdFromVendorPropertyId($request->query->get('vendorPropertyId'));

        return JsonResponse::create(
            [
                'propertyId' => $propertyId,
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
        $input = new SuggestPropertyInput($request->query->get('term'));

        $suggestions = $this->propertyService->suggestProperty($input);

        $output = [];
        foreach ($suggestions as $suggestion) {
            $output[] = [
                'value' => $suggestion->getAddress(),
                'id' => $suggestion->getVendorId(),
            ];
        }

        return JsonResponse::create(
            $output,
            Response::HTTP_OK
        );
    }
}
