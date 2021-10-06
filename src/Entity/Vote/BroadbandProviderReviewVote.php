<?php

namespace App\Entity\Vote;

use App\Entity\BroadbandProviderReview;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class BroadbandProviderReviewVote extends Vote
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
