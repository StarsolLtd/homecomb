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
class Postcode
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
    private string $postcode;

    /**
     * @var Collection<int, Locale>
     * @ORM\ManyToMany(targetEntity="Locale", mappedBy="postcodes")
     * @ORM\JoinTable(name="locale_postcode")
     */
    private Collection $locales;

    public function __construct()
    {
        $this->locales = new ArrayCollection();
    }

    public function __toString(): string
    {
        return (string) $this->getPostcode();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPostcode(): ?string
    {
        return $this->postcode;
    }

    public function setPostcode(string $postcode): self
    {
        $this->postcode = $postcode;

        return $this;
    }

    /**
     * @return Collection<int, Locale>
     */
    public function getLocales(): Collection
    {
        return $this->locales;
    }

    public function addLocale(Locale $locale): self
    {
        if ($this->locales->contains($locale)) {
            return $this;
        }
        $this->locales[] = $locale;

        return $this;
    }
}
