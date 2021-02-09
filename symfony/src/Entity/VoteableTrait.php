<?php

namespace App\Entity;

use App\Entity\Vote\Vote;
use function count;

trait VoteableTrait
{
    public function getVotesScore(): int
    {
        return $this->getPositiveVotesCount() - $this->getNegativeVotesCount();
    }

    public function getPositiveVotesCount(): int
    {
        return count(
            $this->getVotes()->filter(function (Vote $vote) {
                return $vote->isPositive();
            })
        );
    }

    public function getNegativeVotesCount(): int
    {
        return count(
            $this->getVotes()->filter(function (Vote $vote) {
                return !$vote->isPositive();
            })
        );
    }
}
