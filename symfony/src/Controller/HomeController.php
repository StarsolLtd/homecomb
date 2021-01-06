<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route ("/about", name="about", methods={"GET"})
     * @Route ("/contact", name="contact", methods={"GET"})
     * @Route ("/privacy-policy", name="privacy-policy", methods={"GET"})
     * @Route ("/review", name="review", methods={"GET"})
     */
    public function about(): Response
    {
        return $this->render('index.html.twig');
    }

    /**
     * @Route (
     *     "/",
     *     name="app_home",
     *     methods={"GET"}
     * )
     */
    public function home(): Response
    {
        return $this->render('index.html.twig');
    }
}
