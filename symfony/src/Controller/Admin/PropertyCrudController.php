<?php

namespace App\Controller\Admin;

use App\Entity\Property;

class PropertyCrudController extends AppCrudController
{
    public static function getEntityFqcn(): string
    {
        return Property::class;
    }
}
