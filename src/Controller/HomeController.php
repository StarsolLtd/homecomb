<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route ("/", name="app_home", methods={"GET", "HEAD"})
     * @Route ("/about", name="about", methods={"GET", "HEAD"})
     * @Route ("/contact", name="contact", methods={"GET", "HEAD"})
     * @Route ("/privacy-policy", name="privacy-policy", methods={"GET", "HEAD"})
     * @Route ("/find-by-postcode", name="find-by-postcode", methods={"GET", "HEAD"})
     * @Route ("/review", name="review", methods={"GET", "HEAD"})
     * @Route ("/s/{slug}", name="survey", methods={"GET", "HEAD"})
     */
    public function index(): Response
    {
        return $this->render('index.html.twig');
    }
}
