<?php

namespace App\Controller\Admin;

use App\Entity\Branch;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class BranchCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Branch::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
