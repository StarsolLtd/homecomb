<?php

namespace App\Entity\Survey;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Survey\AnswerRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 */
class Answer
{
    use SoftDeleteableEntity;
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity="Question", inversedBy="answers")
     * @ORM\JoinColumn(name="question_id", referencedColumnName="id", nullable=false)
     */
    private Question $question;

    /**
     * @ORM\ManyToOne(targetEntity="Response", inversedBy="answers")
     * @ORM\JoinColumn(name="response_id", referencedColumnName="id", nullable=false)
     */
    private Response $response;

    /**
     * @var Collection<int, Choice>
     * @ORM\ManyToMany(targetEntity="Choice", inversedBy="answers", cascade={"persist"})
     * @ORM\JoinTable(name="answer_choice")
     */
    private Collection $choices;

    /**
     * @ORM\Column(type="text", length=65535, nullable=true)
     */
    private ?string $content;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $rating;

    public function __construct()
    {
        $this->choices = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getQuestion(): Question
    {
        return $this->question;
    }

    public function setQuestion(Question $question): self
    {
        $this->question = $question;

        return $this;
    }

    public function getResponse(): Response
    {
        return $this->response;
    }

    public function setResponse(Response $response): self
    {
        $this->response = $response;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(?int $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * @return Collection<int, Choice>
     */
    public function getChoices(): Collection
    {
        return $this->choices;
    }

    public function addChoice(Choice $choice): self
    {
        if ($this->choices->contains($choice)) {
            return $this;
        }
        $choice->addAnswer($this);
        $this->choices[] = $choice;

        return $this;
    }
}
