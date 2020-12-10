<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LocaleRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 */
class Locale
{
    use SoftDeleteableEntity;
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private string $name;

    /**
     * @ORM\Column(type="text", length=65535, nullable=true)
     */
    private ?string $content;

    /**
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private string $slug;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default": true})
     */
    private bool $published = true;

    /**
     * @var Collection<int, Postcode>
     * @ORM\ManyToMany(targetEntity="Postcode", inversedBy="locales", cascade={"persist"})
     * @ORM\JoinTable(name="locale_postcode")
     */
    private Collection $postcodes;

    public function __construct()
    {
        $this->postcodes = new ArrayCollection();
    }

    public function __toString(): string
    {
        return (string) $this->getName();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    public function getSlug(): ?string
    {
        return $this->slug;
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
     * @return Collection<int, Postcode>
     */
    public function getPostcodes(): Collection
    {
        return $this->postcodes;
    }

    public function addPostcode(Postcode $postcode): self
    {
        if ($this->postcodes->contains($postcode)) {
            return $this;
        }
        $postcode->addLocale($this);
        $this->postcodes[] = $postcode;

        return $this;
    }
}
