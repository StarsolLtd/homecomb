<?php

namespace App\Entity\Locale;

use App\Entity\City;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Locale\CityLocaleRepository")
 */
class CityLocale extends Locale
{
    /**
     * @ORM\OneToOne(targetEntity="App\Entity\City")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     */
    private City $city;

    public function getCity(): City
    {
        return $this->city;
    }

    public function setCity(City $city): self
    {
        $this->city = $city;
        $city->setLocale($this);

        return $this;
    }
}
