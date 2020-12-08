<?php

namespace App\Controller\Admin;

use App\Entity\Review;

class ReviewCrudController extends AppCrudController
{
    public static function getEntityFqcn(): string
    {
        return Review::class;
    }
}
