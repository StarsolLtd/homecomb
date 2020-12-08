<?php

namespace App\Controller;

use App\Exception\NotFoundException;
use App\Model\SuggestPropertyInput;
use App\Repository\PropertyRepository;
use App\Service\PropertyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class PropertyController extends AbstractController
{
    private PropertyRepository $propertyRepository;
    private PropertyService $propertyService;
    private SerializerInterface $serializer;

    public function __construct(
        PropertyRepository $propertyRepository,
        PropertyService $propertyService,
        SerializerInterface $serializer
    ) {
        $this->propertyRepository = $propertyRepository;
        $this->propertyService = $propertyService;
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

    /**
     * @Route (
     *     "/property/{slug}",
     *     name="property-view-by-slug",
     *     methods={"GET", "HEAD"}
     * )
     */
    public function viewBySlug(string $slug): Response
    {
        try {
            $property = $this->propertyRepository->findOnePublishedBySlug($slug);
        } catch (NotFoundException $e) {
            throw $this->createNotFoundException($e->getMessage());
        }

        return $this->render(
            'property/view.html.twig',
            [
                'property' => $property,
            ]
        );
    }
}
