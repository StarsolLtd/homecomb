<?php

namespace App\Service;

use ReCaptcha\ReCaptcha;

class GoogleReCaptchaService
{
    private bool $checkNotRobot;
    private ReCaptcha $reCaptcha;

    public function __construct(
        bool $checkNotRobot,
        ReCaptcha $reCaptcha
    ) {
        $this->checkNotRobot = $checkNotRobot;
        $this->reCaptcha = $reCaptcha;
    }

    public function checkNeeded(): bool
    {
        return $this->checkNotRobot;
    }

    public function verify(?string $token, ?string $clientIp, string $expectedHostname): bool
    {
        if (!$this->checkNeeded()) {
            return true;
        }

        if (null == $token) {
            return false;
        }

        $resp = $this->reCaptcha->setExpectedHostname($expectedHostname)
            ->setScoreThreshold(0.5)
            ->verify($token, $clientIp);

        return $resp->isSuccess();
    }
}
