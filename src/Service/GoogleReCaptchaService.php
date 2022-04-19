<?php

namespace App\Service;

use ReCaptcha\ReCaptcha;

class GoogleReCaptchaService
{
    public const THRESHOLD = .5;

    public function __construct(
        private bool $checkNotRobot,
        private ReCaptcha $reCaptcha,
    ) {
    }

    public function verify(?string $token, ?string $clientIp, string $expectedHostname): bool
    {
        if (!$this->checkNeeded()) {
            return true;
        }

        if (null === $token) {
            return false;
        }

        $resp = $this->reCaptcha->setExpectedHostname($expectedHostname)
            ->setScoreThreshold(self::THRESHOLD)
            ->verify($token, $clientIp);

        return $resp->isSuccess();
    }

    private function checkNeeded(): bool
    {
        return $this->checkNotRobot;
    }
}
