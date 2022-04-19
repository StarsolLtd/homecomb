<?php

namespace App\Controller\Api;

use App\Controller\AppController;
use App\Model\SuggestPropertyInput;
use App\Service\PropertyAutocompleteService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class PropertyAutocompleteController extends AppController
{
    public function __construct(
        private PropertyAutocompleteService $propertyAutocompleteService,
    ) {
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

        $input = new SuggestPropertyInput((string) $term);

        $suggestions = $this->propertyAutocompleteService->search($input->getSearch());

        $output = [];
        foreach ($suggestions as $suggestion) {
            $output[] = [
                'value' => $suggestion->getAddress(),
                'id' => $suggestion->getVendorId(),
                'slug' => $suggestion->getPropertySlug(),
            ];
        }

        return new JsonResponse(
            $output,
            Response::HTTP_OK
        );
    }
}
