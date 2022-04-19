<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SessionService
{
    public function __construct(
        private SessionInterface $session,
    ) {
    }

    /**
     * @return mixed
     */
    public function get(string $name)
    {
        return $this->session->get($name);
    }

    /**
     * @param mixed $value
     */
    public function set(string $name, $value): void
    {
        $this->session->set($name, $value);
    }
}
