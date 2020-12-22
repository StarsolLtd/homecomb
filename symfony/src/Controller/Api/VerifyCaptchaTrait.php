<?php

namespace App\Controller\Api;

use App\Service\GoogleReCaptchaService;
use Symfony\Component\HttpFoundation\Request;

trait VerifyCaptchaTrait
{
    private GoogleReCaptchaService $googleReCaptchaService;

    private function verifyCaptcha(?string $token, Request $request): bool
    {
        return $this->googleReCaptchaService->verify($token, $request->getClientIp(), $request->getHost());
    }
}
