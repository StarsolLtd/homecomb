<?php

namespace App\Entity\Review;

use App\Entity\BroadbandProvider;
use App\Entity\Postcode;
use App\Entity\Vote\BroadbandProviderReviewVote;
use App\Entity\Vote\Vote;
use App\Exception\DeveloperException;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class BroadbandProviderReview extends Review
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\BroadbandProvider")
     * @ORM\JoinColumn(name="related_entity_id", referencedColumnName="id")
     */
    private BroadbandProvider $broadbandProvider;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Postcode")
     * @ORM\JoinColumn(name="postcode", referencedColumnName="id")
     */
    private Postcode $postcode;

    /**
     * @var Collection<int, Vote>
     * @ORM\OneToMany(targetEntity="App\Entity\Vote\BroadbandProviderReviewVote", mappedBy="broadbandProviderReview")
     */
    protected Collection $votes;

    public function addVote(Vote $vote): self
    {
        if ($this->votes->contains($vote)) {
            return $this;
        }
        if (!($vote instanceof BroadbandProviderReviewVote)) {
            throw new DeveloperException('Only BroadbandProviderReviewVotes can be added to a BroadbandProviderReview');
        }
        $this->votes->add($vote);
        $vote->setBroadbandProviderReview($this);

        return $this;
    }

    public function getBroadbandProvider(): BroadbandProvider
    {
        return $this->broadbandProvider;
    }

    public function setBroadbandProvider(BroadbandProvider $broadbandProvider): self
    {
        $this->broadbandProvider = $broadbandProvider;

        return $this;
    }

    public function getPostcode(): Postcode
    {
        return $this->postcode;
    }

    public function setPostcode(Postcode $postcode): self
    {
        $this->postcode = $postcode;

        return $this;
    }
}
