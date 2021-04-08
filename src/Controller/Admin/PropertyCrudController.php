<?php

namespace App\Controller\Admin;

use App\Entity\Property;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PropertyCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Property::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('addressLine1'),
            TextField::new('addressLine2'),
            TextField::new('addressLine3'),
            TextField::new('city'),
            TextField::new('postcode'),
            TextField::new('countryCode'),
            TextField::new('vendorPropertyId')->hideOnIndex()->hideOnForm(),
            TextField::new('slug')->hideOnIndex()->hideOnForm(),
            BooleanField::new('published'),
        ];
    }
}
