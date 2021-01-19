<?php

namespace App\Entity\Survey;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Survey\ResponseRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 */
class Response
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
     * @ORM\ManyToOne(targetEntity="Survey", inversedBy="responses")
     * @ORM\JoinColumn(name="survey_id", referencedColumnName="id", nullable=false)
     */
    private Survey $survey;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="responses")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    private ?User $user = null;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $User): self
    {
        $this->user = $User;

        return $this;
    }
}
