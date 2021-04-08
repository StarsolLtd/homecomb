<?php

namespace App\Controller\Admin;

use App\Entity\Image;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ImageCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Image::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setEntityPermission('ROLE_ADMIN');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            ImageField::new('image')
                ->setBasePath('images/images')
                ->setUploadDir('public/images/images')
                ->setUploadedFileNamePattern('[randomhash].[extension]'),
            TextField::new('description'),
            TextField::new('type'),
            AssociationField::new('agency'),
            AssociationField::new('branch'),
            AssociationField::new('locale'),
            AssociationField::new('review'),
            AssociationField::new('user'),
        ];
    }
}
