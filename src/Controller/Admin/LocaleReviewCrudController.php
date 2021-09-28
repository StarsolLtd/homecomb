<?php

namespace App\Controller\Admin;

use App\Entity\Review\LocaleReview;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

final class LocaleReviewCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return LocaleReview::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('author'),
            TextField::new('title'),
            TextareaField::new('content'),
            IntegerField::new('overallStars'),
            BooleanField::new('published'),
        ];
    }
}
