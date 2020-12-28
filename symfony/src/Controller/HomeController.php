<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route (
     *     "/about",
     *     name="about",
     *     methods={"GET"}
     * )
     */
    public function about(Request $request): Response
    {
        return $this->render('index.html.twig');
    }

    /**
     * @Route (
     *     "/contact",
     *     name="contact",
     *     methods={"GET"}
     * )
     */
    public function contact(Request $request): Response
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
    public function home(Request $request): Response
    {
        return $this->render('index.html.twig');
    }

    /**
     * @Route (
     *     "/privacy-policy",
     *     name="privacyPolicy",
     *     methods={"GET"}
     * )
     */
    public function privacyPolicy(Request $request): Response
    {
        return $this->render('home/privacy-policy.html.twig');
    }
}
