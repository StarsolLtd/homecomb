<?php

namespace App\Controller\Admin;

use App\Entity\Agency;
use App\Entity\Branch;
use App\Entity\Flag;
use App\Entity\Property;
use App\Entity\Review;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('HomeComb');
    }

    public function configureMenuItems(): iterable
    {
        return [
            MenuItem::linkToDashboard('Dashboard', 'fa fa-home'),

            MenuItem::section('Agencies'),
            MenuItem::linkToCrud('Agencies', 'fa fa-building', Agency::class),
            MenuItem::linkToCrud('Branches', 'fa fa-store', Branch::class),

            MenuItem::section('Properties'),
            MenuItem::linkToCrud('Properties', 'fa fa-home', Property::class),

            MenuItem::section('Content'),
            MenuItem::linkToCrud('Reviews', 'fa fa-comment', Review::class),
            MenuItem::linkToCrud('Flags', 'fa fa-flag', Flag::class),
        ];
    }
}
