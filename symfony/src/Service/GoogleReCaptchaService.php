<?php

namespace App\Service;

use ReCaptcha\ReCaptcha;

class GoogleReCaptchaService
{
    private ReCaptcha $reCaptcha;

    public function __construct(
        ReCaptcha $reCaptcha
    ) {
        $this->reCaptcha = $reCaptcha;
    }

    public function verify(?string $token, ?string $clientIp, string $expectedHostname): bool
    {
        if (null == $token) {
            return false;
        }

        $resp = $this->reCaptcha->setExpectedHostname($expectedHostname)
            ->setScoreThreshold(0.5)
            ->verify($token, $clientIp);

        return $resp->isSuccess();
    }
}
