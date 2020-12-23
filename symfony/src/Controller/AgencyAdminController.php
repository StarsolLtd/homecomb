<?php

namespace App\Controller;

use App\Repository\AgencyRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AgencyAdminController extends AppController
{
    private AgencyRepository $agencyRepository;

    public function __construct(
        AgencyRepository $agencyRepository
    ) {
        $this->agencyRepository = $agencyRepository;
    }

    /**
     * @Route (
     *     "/verified/agency",
     *     name="create-agency-form",
     *     methods={"GET"}
     * )
     */
    public function createAgencyForm(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->render(
            'agency_admin/create_agency.html.twig',
        );
    }
}