<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AgencyAdminController extends AppController
{
    /**
     * @Route ("/verified/agency/create", name="verified-agency-created", methods={"GET", "HEAD"})
     * @Route ("/verified/agency", name="verified-agency", methods={"GET", "HEAD"})
     * @Route ("/verified/dashboard", name="verified-dashboard", methods={"GET", "HEAD"})
     * @Route ("/verified/branch", name="verified-branch", methods={"GET", "HEAD"})
     * @Route ("/verified/branch/{slug}", name="verified-branch-slug", methods={"GET", "HEAD"})
     * @Route ("/verified/request-review", name="verified-request-review", methods={"GET", "HEAD"})
     * @Route ("/verified/review/{id}", name="verified-review-id", methods={"GET", "HEAD"})
     */
    public function agencyAdmin(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->render('agency-admin.html.twig');
    }
}
