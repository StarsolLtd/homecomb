<?php

namespace App\Controller\Admin;

use App\Entity\Agency;
use App\Entity\Branch;
use App\Entity\Flag\Flag;
use App\Entity\Image;
use App\Entity\Property;
use App\Entity\Review;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        /** @var CrudUrlGenerator $crudUrlGenerator */
        $crudUrlGenerator = $this->get(CrudUrlGenerator::class);
        $routeBuilder = $crudUrlGenerator->build();

        return $this->redirect($routeBuilder->setController(ReviewCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('HomeComb');
    }

    public function configureMenuItems(): iterable
    {
        return [
            MenuItem::section('Content'),
            MenuItem::linkToCrud('Reviews', 'fa fa-comment', Review::class),
            MenuItem::linkToCrud('Flags', 'fa fa-flag', Flag::class),

            MenuItem::section('Agencies'),
            MenuItem::linkToCrud('Agencies', 'fa fa-building', Agency::class),
            MenuItem::linkToCrud('Branches', 'fa fa-store', Branch::class),

            MenuItem::section('Properties'),
            MenuItem::linkToCrud('Properties', 'fa fa-home', Property::class),

            MenuItem::section('Assets'),
            MenuItem::linkToCrud('Images', 'fa fa-image', Image::class),
        ];
    }
}
