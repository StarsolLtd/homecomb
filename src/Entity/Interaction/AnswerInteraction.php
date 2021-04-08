<?php

namespace App\Entity\Interaction;

use App\Entity\Survey\Answer;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class AnswerInteraction extends Interaction
{
    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Survey\Answer")
     * @ORM\JoinColumn(name="entity_id", referencedColumnName="id")
     */
    private Answer $answer;

    public function getAnswer(): Answer
    {
        return $this->answer;
    }

    public function setAnswer(Answer $answer): self
    {
        $this->answer = $answer;

        return $this;
    }
}
