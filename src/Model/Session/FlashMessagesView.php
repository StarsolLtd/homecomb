<?php

namespace App\Model\Session;

class FlashMessagesView
{
    /**
     * @param FlashMessage[] $messages
     */
    public function __construct(
        private array $messages,
    ) {
    }

    /**
     * @return FlashMessage[]
     */
    public function getMessages(): array
    {
        return $this->messages;
    }
}
