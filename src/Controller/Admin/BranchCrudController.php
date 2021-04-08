<?php

namespace App\Controller\Admin;

use App\Entity\Branch;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class BranchCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Branch::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('agency')->setRequired(true)->autocomplete(),
            TextField::new('name'),
            TextField::new('slug')->hideOnIndex()->hideOnForm(),
            BooleanField::new('published'),
        ];
    }
}
