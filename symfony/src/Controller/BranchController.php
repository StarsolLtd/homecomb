<?php

namespace App\Controller;

use App\Exception\NotFoundException;
use App\Repository\BranchRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BranchController extends AbstractController
{
    private BranchRepository $branchRepository;

    public function __construct(
        BranchRepository $branchRepository
    ) {
        $this->branchRepository = $branchRepository;
    }

    /**
     * @Route (
     *     "/branch/{slug}",
     *     name="branch-view-by-slug",
     *     methods={"GET", "HEAD"}
     * )
     */
    public function viewBySlug(string $slug): Response
    {
        try {
            $branch = $this->branchRepository->findOnePublishedBySlug($slug);
        } catch (NotFoundException $e) {
            throw $this->createNotFoundException($e->getMessage());
        }

        return $this->render('index.html.twig');
    }
}
