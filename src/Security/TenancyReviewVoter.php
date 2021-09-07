<?php

namespace App\Security;

use App\Entity\TenancyReview;
use App\Entity\User;
use App\Exception\DeveloperException;
use App\Service\UserService;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TenancyReviewVoter extends Voter
{
    public const COMMENT = 'comment';

    public function __construct(
        private UserService $userService,
    ) {
    }

    protected function supports(string $attribute, $subject): bool
    {
        if (!in_array($attribute, [self::COMMENT])) {
            return false;
        }

        if (!$subject instanceof TenancyReview) {
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

        /** @var TenancyReview $tenancyReview */
        $tenancyReview = $subject;

        switch ($attribute) {
            case self::COMMENT:
                return $this->canComment($tenancyReview, $user);
        }

        // @codeCoverageIgnoreStart
        throw new DeveloperException('Unsupported attribute.');
        // @codeCoverageIgnoreEnd
    }

    private function canComment(TenancyReview $tenancyReview, User $user): bool
    {
        if (!$tenancyReview->isPublished()) {
            return false;
        }
        $reviewAgency = $tenancyReview->getAgency();
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
