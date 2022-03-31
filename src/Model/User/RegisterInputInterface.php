<?php

namespace App\Model\User;

interface RegisterInputInterface
{
    public function getEmail(): string;

    public function getFirstName(): string;

    public function getLastName(): string;

    public function getPlainPassword(): string;
}
