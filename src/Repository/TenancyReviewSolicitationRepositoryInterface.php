<?php

namespace App\Repository;

use App\Entity\TenancyReviewSolicitation;

interface TenancyReviewSolicitationRepositoryInterface
{
    /**
     * @param mixed $id
     * @param mixed $lockMode
     * @param mixed $lockVersion
     *
     * @return TenancyReviewSolicitation|null
     */
    public function find($id, $lockMode = null, $lockVersion = null);

    public function findOneUnfinishedByCode(string $code): TenancyReviewSolicitation;

    public function findOneByCodeOrNull(string $code): ?TenancyReviewSolicitation;
}
