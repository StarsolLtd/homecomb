<?php

namespace App\Entity\Vote;

use App\Entity\LocaleReview;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class LocaleReviewVote extends Vote
{
    /**
     * @ORM\OneToOne(targetEntity="App\Entity\LocaleReview")
     * @ORM\JoinColumn(name="entity_id", referencedColumnName="id")
     */
    private LocaleReview $localeReview;

    public function getLocaleReview(): LocaleReview
    {
        return $this->localeReview;
    }

    public function setLocaleReview(LocaleReview $localeReview): self
    {
        $this->localeReview = $localeReview;

        return $this;
    }
}
