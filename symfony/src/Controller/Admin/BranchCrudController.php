<?php

namespace App\Controller\Admin;

use App\Entity\Branch;

class BranchCrudController extends AppCrudController
{
    public static function getEntityFqcn(): string
    {
        return Branch::class;
    }
}
