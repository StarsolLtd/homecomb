<?php

namespace App\Controller;

use App\Exception\NotFoundException;
use App\Repository\PropertyRepository;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PropertyController extends AppController
{
    private PropertyRepository $propertyRepository;
    private UserService $userService;

    public function __construct(
        PropertyRepository $propertyRepository,
        UserService $userService
    ) {
        $this->propertyRepository = $propertyRepository;
        $this->userService = $userService;
    }

    /**
     * @Route (
     *     "/property/{slug}",
     *     name="property-view-by-slug",
     *     methods={"GET", "HEAD"}
     * )
     */
    public function viewBySlug(string $slug): Response
    {
        try {
            $property = $this->propertyRepository->findOnePublishedBySlug($slug);
        } catch (NotFoundException $e) {
            throw $this->createNotFoundException($e->getMessage());
        }

        return $this->render('index.html.twig');
    }
}
