<?php

namespace App\Controller;

use App\Model\LookupPropertyIdInput;
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
}
