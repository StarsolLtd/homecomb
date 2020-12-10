<?php

namespace App\Controller;

use App\Exception\NotFoundException;
use App\Repository\LocaleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LocaleController extends AbstractController
{
    private LocaleRepository $localeRepository;

    public function __construct(
        LocaleRepository $localeRepository
    ) {
        $this->localeRepository = $localeRepository;
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
        try {
            $locale = $this->localeRepository->findOnePublishedBySlug($slug);
        } catch (NotFoundException $e) {
            throw $this->createNotFoundException($e->getMessage());
        }

        return $this->render(
            'locale/view.html.twig',
            [
                'locale' => $locale,
            ]
        );
    }
}
