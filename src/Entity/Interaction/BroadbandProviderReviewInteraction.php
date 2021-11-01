<?php

namespace App\Entity\Interaction;

use App\Entity\BroadbandProviderReview;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class BroadbandProviderReviewInteraction extends Interaction
{
    /**
     * @ORM\OneToOne(targetEntity="App\Entity\BroadbandProviderReview")
     * @ORM\JoinColumn(name="entity_id", referencedColumnName="id")
     */
    private BroadbandProviderReview $broadbandProviderReview;

    public function getBroadbandProviderReview(): BroadbandProviderReview
    {
        return $this->broadbandProviderReview;
    }

    public function setBroadbandProviderReview(BroadbandProviderReview $broadbandProviderReview): self
    {
        $this->broadbandProviderReview = $broadbandProviderReview;

        return $this;
    }
}
