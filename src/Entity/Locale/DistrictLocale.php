<?php

namespace App\Entity\Locale;

use App\Entity\District;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Locale\DistrictLocaleRepository")
 */
class DistrictLocale extends Locale
{
    /**
     * @ORM\OneToOne(targetEntity="App\Entity\District")
     * @ORM\JoinColumn(name="district_id", referencedColumnName="id")
     */
    private District $district;

    public function getDistrict(): District
    {
        return $this->district;
    }

    public function setDistrict(District $district): self
    {
        $this->district = $district;

        return $this;
    }
}
