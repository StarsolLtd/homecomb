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
     *     "/api/property/suggest-property",
     *     name="suggest-property",
     *     methods={"GET"}
     * )
     */
    public function suggestProperty(Request $request): JsonResponse
    {
        /** @var SuggestPropertyInput $input */
        $input = $this->serializer->deserialize($request->getContent(), SuggestPropertyInput::class, 'json');

        $suggestions = $this->propertyService->suggestProperty($input);

        $output = [];
        foreach ($suggestions as $suggestion) {
            $output[] = [
                'address' => $suggestion->getAddress(),
                'vendorId' => $suggestion->getVendorId(),
            ];
        }

        return JsonResponse::create(
            $output,
            Response::HTTP_OK
        );
    }
}
