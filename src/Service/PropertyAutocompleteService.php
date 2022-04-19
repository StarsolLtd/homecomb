<?php

namespace App\Service;

use App\Model\Property\PropertySuggestion;
use App\Repository\PropertyRepositoryInterface;

class PropertyAutocompleteService
{
    public function __construct(
        private PropertyRepositoryInterface $propertyRepository,
        private GetAddressService $getAddressService,
    ) {
    }

    /**
     * @return PropertySuggestion[]
     */
    public function search(string $searchQuery): array
    {
        $suggestions = $this->getAddressService->autocomplete($searchQuery);
        $appDatabaseProperties = $this->propertyRepository->findBySearchQuery($searchQuery, 3);

        $suggestionsFoundInAppDatabase = [];

        foreach ($appDatabaseProperties as $property) {
            $vendorPropertyId = $property->getVendorPropertyId();
            if (null !== $vendorPropertyId && $this->isVendorPropertyIdAlreadySuggested($vendorPropertyId, $suggestions)) {
                $suggestionsFoundInAppDatabase[] = $vendorPropertyId;
                continue;
            }

            $suggestions[] = new PropertySuggestion(
                implode(', ', [$property->getAddressLine1(), $property->getPostcode()]),
                $vendorPropertyId,
                $property->getSlug()
            );
        }

        // Sort so that suggestions that already exist in the app database appear first
        usort(
            $suggestions,
            function (PropertySuggestion $item1, PropertySuggestion $item2) use ($suggestionsFoundInAppDatabase) {
                return
                    in_array($item2->getVendorId(), $suggestionsFoundInAppDatabase)
                    <=>
                    in_array($item1->getVendorId(), $suggestionsFoundInAppDatabase)
                    ;
            }
        );

        return $suggestions;
    }

    /**
     * @param PropertySuggestion[] $suggestions
     */
    private function isVendorPropertyIdAlreadySuggested(string $vendorPropertyId, array $suggestions): bool
    {
        $existing = array_filter(
            $suggestions,
            function ($suggestion) use ($vendorPropertyId) {
                return $suggestion->getVendorId() === $vendorPropertyId;
            }
        );

        return [] !== $existing;
    }
}
