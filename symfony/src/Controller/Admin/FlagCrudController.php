<?php

namespace App\Controller\Admin;

use App\Entity\Flag;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class FlagCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Flag::class;
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
