<?php

namespace App\Factory;

use App\Model\Interaction\RequestDetails;
use Symfony\Component\HttpFoundation\Request;

class InteractionFactory
{
    public function getRequestDetails(Request $request): RequestDetails
    {
        return new RequestDetails(
            $request->getSession()->getId(),
            $request->getClientIp(),
            $request->headers->get('User-Agent') ?? null
        );
    }
}
