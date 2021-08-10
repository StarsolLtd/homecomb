<?php

namespace App\Factory;

use App\Entity\Agency;
use App\Entity\Branch;
use App\Entity\City;
use App\Entity\Comment\Comment;
use App\Entity\District;
use App\Entity\Property;
use App\Entity\User;
use App\Model\Agency\Flat as FlatAgency;
use App\Model\Branch\Flat as FlatBranch;
use App\Model\City\Flat as FlatCity;
use App\Model\Comment\Flat as FlatComment;
use App\Model\District\Flat as FlatDistrict;
use App\Model\Property\Flat as FlatProperty;
use App\Model\User\Flat as FlatUser;

class FlatModelFactory
{
    public function getBranchFlatModel(Branch $entity): FlatBranch
    {
        return new FlatBranch(
            $entity->getSlug(),
            $entity->getName(),
            $entity->isPublished(),
            $entity->getTelephone(),
            $entity->getEmail()
        );
    }

    public function getAgencyFlatModel(Agency $entity): FlatAgency
    {
        $logoImage = $entity->getLogoImage();
        $logoImageFilename = $logoImage ? $logoImage->getImage() : null;

        return new FlatAgency(
            $entity->getSlug(),
            $entity->getName(),
            $entity->getExternalUrl(),
            $entity->getPostcode(),
            $entity->isPublished(),
            $logoImageFilename
        );
    }

    public function getCityFlatModel(City $entity): FlatCity
    {
        return new FlatCity(
            $entity->getSlug(),
            $entity->getName(),
            $entity->getCounty(),
            $entity->getCountryCode(),
            $entity->isPublished(),
        );
    }

    public function getCommentFlatModel(Comment $entity): FlatComment
    {
        $user = $entity->getUser();

        return new FlatComment(
            $entity->getId(),
            $user->getFirstName().' '.$user->getLastName(),
            $entity->getContent(),
            $entity->getCreatedAt(),
        );
    }

    public function getDistrictFlatModel(District $entity): FlatDistrict
    {
        return new FlatDistrict(
            $entity->getSlug(),
            $entity->getName(),
            $entity->getCounty(),
            $entity->getCountryCode(),
            $entity->getType(),
            $entity->isPublished(),
        );
    }

    public function getPropertyFlatModel(Property $entity): FlatProperty
    {
        return new FlatProperty(
            $entity->getSlug(),
            $entity->getAddressLine1() ?? '',
            $entity->getPostcode()
        );
    }

    public function getUserFlatModel(User $entity): FlatUser
    {
        return new FlatUser(
            $entity->getUsername(),
            $entity->getTitle(),
            $entity->getFirstName(),
            $entity->getLastName(),
            null !== $entity->getAdminAgency(),
        );
    }
}
