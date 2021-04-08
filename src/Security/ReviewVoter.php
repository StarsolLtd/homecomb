<?php

namespace App\Security;

use App\Entity\Review;
use App\Entity\User;
use App\Exception\DeveloperException;
use App\Service\UserService;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ReviewVoter extends Voter
{
    private UserService $userService;

    const COMMENT = 'comment';

    public function __construct(
        UserService $userService
    ) {
        $this->userService = $userService;
    }

    protected function supports(string $attribute, $subject): bool
    {
        if (!in_array($attribute, [self::COMMENT])) {
            return false;
        }

        if (!$subject instanceof Review) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        /** @var Review $review */
        $review = $subject;

        switch ($attribute) {
            case self::COMMENT:
                return $this->canComment($review, $user);
        }

        // @codeCoverageIgnoreStart
        throw new DeveloperException('Unsupported attribute.');
        // @codeCoverageIgnoreEnd
    }

    private function canComment(Review $review, User $user): bool
    {
        if (!$review->isPublished()) {
            return false;
        }
        $reviewAgency = $review->getAgency();
        if (null === $reviewAgency) {
            return false;
        }
        $user = $this->userService->getUserEntityOrNullFromUserInterface($user);
        if (null === $user) {
            return false;
        }
        $userAgency = $user->getAdminAgency();
        if (null === $userAgency) {
            return false;
        }
        if ($reviewAgency->getId() !== $userAgency->getId()) {
            return false;
        }

        return true;
    }
}
