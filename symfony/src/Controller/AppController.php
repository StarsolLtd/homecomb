<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\SerializerInterface;

abstract class AppController extends AbstractController
{
    protected SerializerInterface $serializer;

    protected function getUserInterface(): ?UserInterface
    {
        $user = $this->getUser();
        if ($user instanceof UserInterface) {
            return $user;
        }

        return null;
    }

    protected function jsonResponse(?object $model, int $responseCode): JsonResponse
    {
        return new JsonResponse(
            $this->serializer->serialize($model, 'json'),
            $responseCode,
            [],
            true
        );
    }

    protected function addDeserializationFailedFlashMessage(): void
    {
        $this->addFlash(
            'error',
            'Sorry, we were unable to process your request. The data provided was malformed.'
        );
    }
}
