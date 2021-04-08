<?php

namespace App\Controller;

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
        return $this->render('index.html.twig');
    }
}
