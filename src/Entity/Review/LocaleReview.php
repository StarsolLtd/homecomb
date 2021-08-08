<?php

namespace App\Entity\Review;

use App\Entity\Locale\Locale;
use App\Entity\Vote\LocaleReviewVote;
use App\Entity\Vote\Vote;
use App\Exception\DeveloperException;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class LocaleReview extends Review
{
    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Locale\Locale")
     * @ORM\JoinColumn(name="related_entity_id", referencedColumnName="id")
     */
    private Locale $locale;

    /**
     * @var Collection<int, Vote>
     * @ORM\OneToMany(targetEntity="App\Entity\Vote\LocaleReviewVote", mappedBy="localeReview")
     */
    protected Collection $votes;

    public function addVote(Vote $vote): self
    {
        if ($this->votes->contains($vote)) {
            return $this;
        }
        if (!($vote instanceof LocaleReviewVote)) {
            throw new DeveloperException('Only LocaleReviewVotes can be added to a LocaleReview');
        }
        $this->votes->add($vote);
        $vote->setLocaleReview($this);

        return $this;
    }

    public function getLocale(): Locale
    {
        return $this->locale;
    }

    public function setLocale(Locale $locale): self
    {
        $this->locale = $locale;

        return $this;
    }
}
