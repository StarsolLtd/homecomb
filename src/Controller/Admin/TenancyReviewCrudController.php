<?php

namespace App\Controller\Admin;

use App\Entity\TenancyReview;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

final class TenancyReviewCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TenancyReview::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('property')->setRequired(true)->autocomplete(),
            AssociationField::new('branch')->setRequired(false)->autocomplete(),
            TextField::new('author'),
            TextField::new('title'),
            TextareaField::new('content'),
            IntegerField::new('overallStars'),
            IntegerField::new('propertyStars'),
            IntegerField::new('agencyStars'),
            IntegerField::new('landlordStars'),
            BooleanField::new('published'),
        ];
    }
}
