<?php

namespace App\Model\Session;

class FlashMessagesView
{
    /** @var FlashMessage[] */
    private array $messages;

    public function __construct(
        array $messages
    ) {
        $this->messages = $messages;
    }

    /**
     * @return FlashMessage[]
     */
    public function getMessages(): array
    {
        return $this->messages;
    }
}
