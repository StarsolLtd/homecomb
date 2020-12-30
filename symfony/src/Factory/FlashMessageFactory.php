<?php

namespace App\Factory;

use App\Model\Session\FlashMessage;
use App\Model\Session\FlashMessagesView;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class FlashMessageFactory
{
    public function getFlashMessages(FlashBagInterface $flashBag): FlashMessagesView
    {
        $messages = [];
        foreach ($flashBag->keys() as $type) {
            foreach ($flashBag->get($type) as $message) {
                $messages[] = new FlashMessage(
                    $type,
                    $message
                );
            }
        }

        return new FlashMessagesView($messages);
    }
}
