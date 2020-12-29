<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AgencyAdminController extends AppController
{
    /**
     * @Route ("/verified/agency/create", name="create-agency-form", methods={"GET"})
     * @Route ("/verified/agency", name="create-agency-form", methods={"GET"})
     * @Route ("/verified/request-review", name="request-review", methods={"GET"})
     * @Route ("/verified/agency-admin", name="agency-admin-home", methods={"GET"})
     */
    public function agencyAdmin(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->render('agency-admin.html.twig');
    }
}
