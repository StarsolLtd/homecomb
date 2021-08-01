<?php

namespace App\Entity\Interaction;

use App\Entity\TenancyReview;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class TenancyReviewInteraction extends Interaction
{
    /**
     * @ORM\OneToOne(targetEntity="App\Entity\TenancyReview")
     * @ORM\JoinColumn(name="entity_id", referencedColumnName="id")
     */
    private TenancyReview $tenancyReview;

    public function getTenancyReview(): TenancyReview
    {
        return $this->tenancyReview;
    }

    public function setTenancyReview(TenancyReview $tenancyReview): self
    {
        $this->tenancyReview = $tenancyReview;

        return $this;
    }
}
