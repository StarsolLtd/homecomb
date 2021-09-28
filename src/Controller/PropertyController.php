<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class PropertyController extends AppController
{
    /**
     * @Route (
     *     "/property/{slug}",
     *     name="property-view-by-slug",
     *     methods={"GET", "HEAD"}
     * )
     */
    public function viewBySlug(string $slug): Response
    {
        return $this->render('index.html.twig');
    }
}
