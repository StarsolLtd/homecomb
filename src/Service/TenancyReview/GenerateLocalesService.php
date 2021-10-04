<?php

namespace App\Service\TenancyReview;

use App\Entity\Locale\Locale;
use App\Entity\Postcode;
use App\Entity\TenancyReview;
use App\Repository\PostcodeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;

class GenerateLocalesService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PostcodeRepository $postcodeRepository,
    ) {
    }

    /**
     * @return Collection<int, Locale>
     */
    public function generateLocales(TenancyReview $tenancyReview): Collection
    {
        /** @var Collection<int, Locale> $locales */
        $locales = new ArrayCollection();

        $fullPostcode = $tenancyReview->getProperty()->getPostcode();
        if (!$fullPostcode) {
            return $locales;
        }

        list($first, $second) = explode(' ', $fullPostcode);

        $findBeginningWithString = [$first];
        // TODO also find beginning with parts of $second. For example, "CB4 3", "CB4 3L" and "CB4 3LF".

        /** @var Collection<int, Postcode> $postcodes */
        $postcodes = new ArrayCollection();
        foreach ($findBeginningWithString as $string) {
            foreach ($this->postcodeRepository->findBeginningWith($string) as $postcode) {
                if (!$postcodes->contains($postcode)) {
                    $postcodes->add($postcode);
                }
            }
        }

        /** @var Postcode $postcode */
        foreach ($postcodes as $postcode) {
            foreach ($postcode->getLocales() as $locale) {
                if (!$locales->contains($locale)) {
                    $locales->add($locale);
                }
            }
        }

        foreach ($locales as $locale) {
            $locale->addTenancyReview($tenancyReview);
        }

        $this->entityManager->flush();

        return $locales;
    }
}
