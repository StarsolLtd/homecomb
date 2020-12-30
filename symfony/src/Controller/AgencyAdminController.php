<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AgencyAdminController extends AppController
{
    /**
     * @Route ("/verified/agency/create", name="verified-agency-created", methods={"GET"})
     * @Route ("/verified/agency", name="verified-agency", methods={"GET"})
     * @Route ("/verified/request-review", name="verified-request-review", methods={"GET"})
     * @Route ("/verified/agency-admin", name="verified-agency-admin", methods={"GET"})
     */
    public function agencyAdmin(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->render('agency-admin.html.twig');
    }
}
