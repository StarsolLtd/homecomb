<?php

namespace App\Service\Branch;

use App\Factory\BranchFactory;
use App\Model\Branch\View;
use App\Repository\BranchRepositoryInterface;

class ViewService
{
    public function __construct(
        private BranchFactory $branchFactory,
        private BranchRepositoryInterface $branchRepository,
    ) {
    }

    public function getViewBySlug(string $slug): View
    {
        $branch = $this->branchRepository->findOnePublishedBySlug($slug);

        return $this->branchFactory->createViewFromEntity($branch);
    }
}
