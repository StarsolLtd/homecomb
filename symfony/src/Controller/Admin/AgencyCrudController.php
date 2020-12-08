<?php

namespace App\Controller\Admin;

use App\Entity\Agency;

class AgencyCrudController extends AppCrudController
{
    public static function getEntityFqcn(): string
    {
        return Agency::class;
    }
}
