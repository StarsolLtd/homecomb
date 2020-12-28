<?php

namespace App\Controller;

use App\Repository\LocaleRepository;
use App\Service\LocaleService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LocaleController extends AbstractController
{
    private LocaleRepository $localeRepository;
    private LocaleService $localeService;

    public function __construct(
        LocaleRepository $localeRepository,
        LocaleService $localeService
    ) {
        $this->localeRepository = $localeRepository;
        $this->localeService = $localeService;
    }

    /**
     * @Route (
     *     "/l/{slug}",
     *     name="locale-view-by-slug",
     *     methods={"GET", "HEAD"}
     * )
     */
    public function viewBySlug(string $slug): Response
    {
        return $this->render('index.html.twig');
    }
}
