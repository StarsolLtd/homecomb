<?php

namespace App\Entity\Survey;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Survey\SurveyRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 */
class Survey
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
     * @ORM\Column(type="string", length=255, unique=false)
     */
    private string $title;

    /**
     * @ORM\Column(type="text", length=65535, nullable=true)
     */
    private ?string $description;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private string $slug;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default": true})
     */
    private bool $published = true;

    /**
     * @var Collection<int, Question>
     * @ORM\OneToMany(targetEntity="Question", mappedBy="survey", cascade={"persist"})
     */
    private Collection $questions;

    /**
     * @var Collection<int, Response>
     * @ORM\OneToMany(targetEntity="Response", mappedBy="survey", cascade={"persist"})
     */
    private Collection $responses;

    public function __construct()
    {
        $this->questions = new ArrayCollection();
        $this->responses = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getTitle();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function isPublished(): bool
    {
        return $this->published;
    }

    public function setPublished(bool $published): self
    {
        $this->published = $published;

        return $this;
    }

    /**
     * @return Collection<int, Question>
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    /**
     * @return Collection<int, Question>
     */
    public function getPublishedQuestions(): Collection
    {
        return $this->getQuestions()->filter(function (Question $question) {
            return $question->isPublished();
        });
    }

    public function addQuestion(Question $question): self
    {
        if ($this->questions->contains($question)) {
            return $this;
        }
        $this->questions[] = $question;
        $question->setSurvey($this);

        return $this;
    }

    /**
     * @return Collection<int, Response>
     */
    public function getResponses(): Collection
    {
        return $this->responses;
    }

    public function addResponse(Response $response): self
    {
        if ($this->responses->contains($response)) {
            return $this;
        }
        $this->responses[] = $response;
        $response->setSurvey($this);

        return $this;
    }
}
