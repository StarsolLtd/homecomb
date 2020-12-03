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
        $propertySlug = $this->propertyService->determinePropertySlugFromVendorPropertyId($request->query->get('vendorPropertyId'));

        return JsonResponse::create(
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
