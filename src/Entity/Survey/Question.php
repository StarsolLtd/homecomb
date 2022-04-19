<?php

namespace App\Entity\Survey;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Survey\QuestionRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 */
class Question
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
     * @ORM\ManyToOne(targetEntity="Survey", inversedBy="questions")
     * @ORM\JoinColumn(name="survey_id", referencedColumnName="id", nullable=false)
     */
    private Survey $survey;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private string $type;

    /**
     * @ORM\Column(type="text", length=65535, nullable=false)
     */
    private string $content;

    /**
     * @ORM\Column(type="text", length=65535, nullable=true)
     */
    private ?string $help;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $highMeaning;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $lowMeaning;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default": true})
     */
    private bool $published = true;

    /**
     * @ORM\Column(type="integer", nullable=false, options={"default": 100})
     */
    private int $sortOrder = 100;

    /**
     * @var Collection<int, Answer>
     * @ORM\OneToMany(targetEntity="Answer", mappedBy="question", cascade={"persist"})
     */
    private Collection $answers;

    /**
     * @var Collection<int, Choice>
     * @ORM\OneToMany(targetEntity="Choice", mappedBy="question", cascade={"persist"})
     */
    private Collection $choices;

    public function __construct()
    {
        $this->answers = new ArrayCollection();
        $this->choices = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getContent();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getSurvey(): Survey
    {
        return $this->survey;
    }

    public function setSurvey(Survey $survey): self
    {
        $this->survey = $survey;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getHelp(): ?string
    {
        return $this->help;
    }

    public function setHelp(?string $help): self
    {
        $this->help = $help;

        return $this;
    }

    public function getHighMeaning(): ?string
    {
        return $this->highMeaning;
    }

    public function setHighMeaning(?string $highMeaning): self
    {
        $this->highMeaning = $highMeaning;

        return $this;
    }

    public function getLowMeaning(): ?string
    {
        return $this->lowMeaning;
    }

    public function setLowMeaning(?string $lowMeaning): self
    {
        $this->lowMeaning = $lowMeaning;

        return $this;
    }

    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    public function setSortOrder(int $sortOrder): self
    {
        $this->sortOrder = $sortOrder;

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
     * @return Collection<int, Answer>
     */
    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    public function addAnswer(Answer $answer): self
    {
        if ($this->answers->contains($answer)) {
            return $this;
        }
        $this->answers[] = $answer;
        $answer->setQuestion($this);

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

        if (100 === $choice->getSortOrder()) {
            $choice->setSortOrder(count($this->choices) + 1);
        }

        $choice->setQuestion($this);
        $this->choices[] = $choice;

        return $this;
    }

    /**
     * @return Collection<int, Choice>
     */
    public function getPublishedChoices(): Collection
    {
        return $this->getChoices()->filter(function (Choice $choice) {
            return $choice->isPublished();
        });
    }
}
