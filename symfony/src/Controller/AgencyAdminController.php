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
     *     "/verified/agency/create",
     *     name="create-agency-form",
     *     methods={"GET"}
     * )
     */
    public function createAgencyForm(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->render('index.html.twig');
    }

    /**
     * @Route (
     *     "/verified/agency",
     *     name="create-agency-form",
     *     methods={"GET"}
     * )
     */
    public function updateAgencyForm(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->render('index.html.twig');
    }

    /**
     * @Route (
     *     "/verified/request-review",
     *     name="request-review",
     *     methods={"GET"}
     * )
     */
    public function reviewSolicitationForm(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->render('index.html.twig');
    }

    /**
     * @Route (
     *     "/verified/agency-admin",
     *     name="agency-admin-home",
     *     methods={"GET"}
     * )
     */
    public function agencyAdminHome(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->render('index.html.twig');
    }
}
